import './styles/app.css';

const CHART_COLORS = ['#2ECC71', '#3498DB', '#F39C12', '#E74C3C', '#9B59B6', '#1ABC9C'];

window.confirmDelete = function confirmDelete(formId) {
	Swal.fire({
		title: 'Confirmer la suppression',
		text: 'Cette action est irréversible.',
		icon: 'warning',
		iconColor: '#E74C3C',
		showCancelButton: true,
		confirmButtonColor: '#E74C3C',
		cancelButtonColor: '#6B7A8E',
		confirmButtonText: '<i class="bi bi-trash"></i> Supprimer',
		cancelButtonText: 'Annuler',
		borderRadius: '16px',
		customClass: {
			popup: 'swal-farm-popup',
			title: 'swal-farm-title'
		}
	}).then((result) => {
		if (result.isConfirmed) {
			const form = document.getElementById(formId);
			if (form) form.submit();
		}
	});
};

const applyChartDefaults = () => {
	if (!window.Chart) return;
	Chart.defaults.font.family = 'Inter, sans-serif';
	Chart.defaults.font.size = 12;
	Chart.defaults.color = '#6B7A8E';
	Chart.defaults.plugins.legend.labels.boxWidth = 10;
	Chart.defaults.responsive = true;
	Chart.defaults.maintainAspectRatio = false;
};

const initSidebar = () => {
	const sidebar = document.getElementById('appSidebar');
	const backdrop = document.getElementById('sidebarBackdrop');
	const toggle = document.getElementById('sidebarToggle');
	if (!sidebar || !backdrop || !toggle) return;

	const close = () => {
		sidebar.classList.remove('show');
		backdrop.classList.remove('show');
		document.body.classList.remove('overflow-hidden');
	};

	toggle.addEventListener('click', () => {
		sidebar.classList.toggle('show');
		backdrop.classList.toggle('show');
		document.body.classList.toggle('overflow-hidden');
	});
	backdrop.addEventListener('click', close);
};

const initSelect2 = () => {
	if (window.$ && $.fn.select2) {
		$('select').select2({ width: '100%' });
	}
};

const initFlatpickr = () => {
	if (!window.flatpickr) return;
	flatpickr('input[type="date"]', {
		dateFormat: 'Y-m-d',
		disableMobile: true
	});
};

const initToasts = () => {
	document.querySelectorAll('.app-toast').forEach((toast) => {
		setTimeout(() => {
			toast.classList.add('dismiss');
			setTimeout(() => toast.remove(), 250);
		}, 4000);
	});
};

const initSubmitLoading = () => {
	document.querySelectorAll('form').forEach((form) => {
		form.addEventListener('submit', () => {
			const btn = form.querySelector('button[type="submit"]');
			if (!btn || btn.dataset.noLoading === '1') return;
			if (btn.dataset.loadingApplied === '1') return;
			btn.dataset.loadingApplied = '1';
			const initial = btn.innerHTML;
			btn.dataset.initialHtml = initial;
			btn.disabled = true;
			btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Chargement...';
		});
	});
};

const initDeleteForms = () => {
	document.querySelectorAll('form.delete-form').forEach((form) => {
		if (!form.id) {
			form.id = `delete-form-${Math.random().toString(36).slice(2)}`;
		}
		form.addEventListener('submit', (event) => {
			event.preventDefault();
			window.confirmDelete(form.id);
		});
	});
};

const initActionButtons = () => {
	document.querySelectorAll('.btn-action').forEach((btn) => {
		const label = btn.dataset.label;
		const icon = btn.dataset.icon;
		if (!label || !icon) return;
		const mobile = window.matchMedia('(max-width: 767.98px)').matches;
		btn.innerHTML = mobile ? `<i class="bi ${icon}"></i>` : `<i class="bi ${icon} me-1"></i>${label}`;
	});
};

const initDataTables = () => {
	if (!(window.$ && $.fn.DataTable)) return;
	$('table.datatable').each(function init() {
		const $table = $(this);
		const serverPagination = $table.data('serverPagination') === 1 || $table.data('serverPagination') === true || $table.data('serverPagination') === '1';
		const wrapper = $table.closest('.table-wrap');
		if (wrapper.length) wrapper.addClass('skeleton');

		$table.DataTable({
			language: { url: '/js/dataTables.french.json' },
			pageLength: 10,
			responsive: true,
			paging: !serverPagination,
			searching: !serverPagination,
			info: !serverPagination,
			lengthChange: !serverPagination,
			dom: serverPagination
				? 't'
				: '<"row"<"col-md-6"l><"col-md-6"f>>t<"row"<"col-md-6"i><"col-md-6"p>>',
			drawCallback: initActionButtons,
			initComplete: function onInit() {
				if (wrapper.length) wrapper.removeClass('skeleton');
				initActionButtons();
			}
		});
	});
};

const animateCounters = () => {
	document.querySelectorAll('[data-countup]').forEach((el) => {
		const target = Number(el.dataset.countup || 0);
		const duration = 900;
		const start = performance.now();
		const initial = 0;

		const tick = (now) => {
			const p = Math.min((now - start) / duration, 1);
			const value = Math.floor(initial + (target - initial) * p);
			el.textContent = value.toLocaleString('fr-FR');
			if (p < 1) requestAnimationFrame(tick);
		};
		requestAnimationFrame(tick);
	});
};

const chartData = (id) => {
	const el = document.getElementById(id);
	if (!el) return null;
	return {
		el,
		items: JSON.parse(el.dataset.chart || '[]')
	};
};

const initCharts = () => {
	if (!window.Chart) return;

	const renderDonut = (id, label) => {
		const data = chartData(id);
		if (!data) return;
		new Chart(data.el, {
			type: 'doughnut',
			data: {
				labels: data.items.map((x) => x.label ?? x.mois),
				datasets: [{
					label,
					data: data.items.map((x) => x.total),
					backgroundColor: CHART_COLORS,
					borderWidth: 0,
					cutout: '60%'
				}]
			},
			options: {
				plugins: {
					legend: {
						position: 'bottom',
						labels: { usePointStyle: true }
					}
				}
			}
		});
	};

	const renderBar = (id) => {
		const data = chartData(id);
		if (!data) return;
		new Chart(data.el, {
			type: 'bar',
			data: {
				labels: data.items.map((x) => x.label),
				datasets: [{
					data: data.items.map((x) => x.total),
					backgroundColor: CHART_COLORS[0],
					borderRadius: 6,
					borderSkipped: false
				}]
			},
			options: {
				plugins: { legend: { display: false } }
			}
		});
	};

	const renderLine = (id) => {
		const data = chartData(id);
		if (!data) return;
		const ctx = data.el.getContext('2d');
		const gradient = ctx.createLinearGradient(0, 0, 0, 220);
		gradient.addColorStop(0, 'rgba(46, 204, 113, 0.18)');
		gradient.addColorStop(1, 'rgba(46, 204, 113, 0)');

		new Chart(data.el, {
			type: 'line',
			data: {
				labels: data.items.map((x) => x.mois),
				datasets: [{
					data: data.items.map((x) => x.total),
					borderColor: CHART_COLORS[0],
					backgroundColor: gradient,
					fill: true,
					tension: 0.4,
					pointRadius: 4,
					pointHoverRadius: 6
				}]
			},
			options: {
				plugins: { legend: { display: false } }
			}
		});
	};

	renderDonut('chartGenre', 'Genre');
	renderDonut('chartRace', 'Race');
	renderDonut('chartOrigine', 'Origine');
	renderBar('chartGrange');
	renderLine('chartEvolution');
};

const updateFactureAchatTotal = () => {
	const holder = document.querySelector('[data-collection-holder]');
	const totalEl = document.getElementById('facture-total-live');
	if (!holder || !totalEl) return;

	let total = 0;
	holder.querySelectorAll('[name*="[prix]"], [name*="[quantite]"]').forEach(() => {
		// noop, trigger iteration through rows below
	});

	holder.querySelectorAll('.sous-lot-row').forEach((row) => {
		const prix = Number((row.querySelector('[name*="[prix]"]')?.value || '0').replace(',', '.'));
		const qte = Number((row.querySelector('[name*="[quantite]"]')?.value || '0').replace(',', '.'));
		total += prix * qte;
	});

	totalEl.textContent = `${total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} TND`;
};

const initFactureLots = () => {
	const addRowBtn = document.getElementById('add-row');
	const holder = document.querySelector('[data-collection-holder]');
	if (!addRowBtn || !holder) return;

	let index = Number(holder.dataset.index || holder.querySelectorAll('.sous-lot-row').length);
	const prototype = holder.dataset.prototype || '';

	const bindRow = (row) => {
		row.classList.add('sous-lot-row');
		row.querySelectorAll('input, select').forEach((input) => {
			input.addEventListener('input', updateFactureAchatTotal);
			input.addEventListener('change', updateFactureAchatTotal);
		});

		const remove = row.querySelector('.btn-remove-lot');
		if (remove) {
			remove.addEventListener('click', () => {
				row.remove();
				updateFactureAchatTotal();
			});
		}
	};

	holder.querySelectorAll('.sous-lot-row').forEach(bindRow);

	addRowBtn.addEventListener('click', () => {
		if (!prototype) return;
		const html = prototype.replace(/__name__/g, String(index));
		index += 1;
		holder.dataset.index = String(index);

		const row = document.createElement('div');
		row.className = 'sous-lot-row panel p-3 mb-2 position-relative';
		row.innerHTML = `${html}<button type="button" class="btn btn-sm btn-danger btn-remove-lot position-absolute top-0 end-0 m-2"><i class="bi bi-x"></i></button>`;
		holder.appendChild(row);
		bindRow(row);
		initSelect2();
		updateFactureAchatTotal();
	});

	updateFactureAchatTotal();
};

document.addEventListener('DOMContentLoaded', () => {
	applyChartDefaults();
	initSidebar();
	initSelect2();
	initFlatpickr();
	initDeleteForms();
	initSubmitLoading();
	initToasts();
	initDataTables();
	initCharts();
	animateCounters();
	initFactureLots();
});
