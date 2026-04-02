import './styles/app.css';

document.addEventListener('DOMContentLoaded', () => {
	if (window.$ && $.fn.DataTable) {
		$('.datatable').DataTable({
			pageLength: 10,
			responsive: true,
			language: { search: 'Recherche:' }
		});
	}

	if (window.$ && $.fn.select2) {
		$('select').select2({ width: '100%' });
	}

	if (window.flatpickr) {
		flatpickr('input[type="date"]', { dateFormat: 'Y-m-d' });
	}

	document.querySelectorAll('.delete-form').forEach((form) => {
		form.addEventListener('submit', (e) => {
			e.preventDefault();
			Swal.fire({
				title: 'Confirmer la suppression ?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#2ECC71',
				cancelButtonColor: '#E74C3C'
			}).then((r) => {
				if (r.isConfirmed) {
					form.submit();
				}
			});
		});
	});

	document.querySelectorAll('.toast').forEach((el) => {
		new bootstrap.Toast(el).show();
	});

	const renderPie = (id, label) => {
		const el = document.getElementById(id);
		if (!el) return;
		const data = JSON.parse(el.dataset.chart || '[]');
		new Chart(el, {
			type: 'pie',
			data: {
				labels: data.map(d => d.label ?? d.mois),
				datasets: [{ label, data: data.map(d => d.total), backgroundColor: ['#1B2A41','#2ECC71','#F39C12','#E74C3C','#243B55'] }]
			}
		});
	};
	const renderBar = (id) => {
		const el = document.getElementById(id);
		if (!el) return;
		const data = JSON.parse(el.dataset.chart || '[]');
		new Chart(el, {
			type: 'bar',
			data: { labels: data.map(d => d.label), datasets: [{ data: data.map(d => d.total), backgroundColor: '#2ECC71' }] }
		});
	};
	const renderLine = (id) => {
		const el = document.getElementById(id);
		if (!el) return;
		const data = JSON.parse(el.dataset.chart || '[]');
		new Chart(el, {
			type: 'line',
			data: { labels: data.map(d => d.mois), datasets: [{ data: data.map(d => d.total), borderColor: '#1B2A41' }] }
		});
	};

	renderPie('chartGenre', 'Genre');
	renderPie('chartRace', 'Race');
	renderPie('chartOrigine', 'Origine');
	renderBar('chartGrange');
	renderLine('chartEvolution');

	const addRowBtn = document.getElementById('add-row');
	const holder = document.querySelector('[data-collection-holder]');
	if (addRowBtn && holder) {
		const ul = holder.querySelector('div');
		let index = ul ? ul.children.length : 0;
		addRowBtn.addEventListener('click', () => {
			const prototype = holder.dataset.prototype || holder.getAttribute('data-prototype');
			if (!prototype) return;
			const html = prototype.replace(/__name__/g, index++);
			const wrapper = document.createElement('div');
			wrapper.className = 'border rounded p-2 mb-2';
			wrapper.innerHTML = html;
			holder.appendChild(wrapper);
		});
	}
});
