const explorers = document.querySelectorAll('.ech-resource-explorer');

explorers.forEach((explorer) => {
	const resultsContainer = explorer.querySelector('.ech-resources');
	const endpoint = explorer.dataset.endpoint;

	if (!endpoint || !resultsContainer) {
		return;
	}

	const searchInput = explorer.querySelector('.ech-resource-search');
	const departmentSelect = explorer.querySelector('.ech-resource-department');
	const loadingElement = explorer.querySelector('.ech-resource-loading');
	const emptyElement = explorer.querySelector('.ech-resource-empty');

	const perPage = explorer.dataset.perPage || '6';
	const showDate = explorer.dataset.showDate === '1';

	let timeout = null;

	async function loadResources() {
		const params = new URLSearchParams();

		params.set('per_page', perPage);
		params.set('page', '1');

		if (searchInput?.value.trim()) {
			params.set('search', searchInput.value.trim());
		}

		if (departmentSelect?.value) {
			params.set('department', departmentSelect.value);
		}

		loadingElement?.removeAttribute('hidden');
		emptyElement?.setAttribute('hidden', '');

		try {
			const url = new URL(endpoint);

			params.forEach((value, key) => {
				url.searchParams.set(key, value);
			});

			const response = await fetch(url.toString(), {
				headers: {
					Accept: 'application/json',
				},
			});

			if (!response.ok) {
				throw new Error('Unable to load resources.');
			}

			const responseData = await response.json();

			renderResources(responseData.data || []);
		} catch (error) {
			renderError();
		} finally {
			loadingElement?.setAttribute('hidden', '');
		}
	}

	function renderResources(resources) {
		resultsContainer.replaceChildren();

		if (!resources.length) {
			emptyElement?.removeAttribute('hidden');
			return;
		}

		emptyElement?.setAttribute('hidden', '');

		resources.forEach((resource) => {
			resultsContainer.appendChild(createResourceCard(resource));
		});
	}

	function createResourceCard(resource) {
		const article = document.createElement('article');

		article.className = 'ech-resource-card';

		const meta = document.createElement('div');

		meta.className = 'ech-resource-meta';

		const departments = Array.isArray(resource.departments)
			? resource.departments
			: [];

		if (departments.length) {
			const department = document.createElement('span');

			department.textContent = departments.join(', ');

			meta.appendChild(department);
		}

		if (showDate && resource.updated_at) {
			const date = document.createElement('span');

			date.textContent = formatDate(resource.updated_at);

			meta.appendChild(date);
		}

		article.appendChild(meta);

		const heading = document.createElement('h3');

		heading.className = 'ech-resource-title';

		const titleLink = document.createElement('a');

		titleLink.href = resource.permalink || '#';
		titleLink.textContent = resource.title || '';

		heading.appendChild(titleLink);
		article.appendChild(heading);

		if (resource.excerpt) {
			const excerpt = document.createElement('div');

			excerpt.className = 'ech-resource-excerpt';
			excerpt.textContent = resource.excerpt;

			article.appendChild(excerpt);
		}

		const resourceLink = document.createElement('a');

		resourceLink.className = 'ech-resource-link';
		resourceLink.href = resource.permalink || '#';
		resourceLink.textContent = 'View Resource →';

		article.appendChild(resourceLink);

		return article;
	}

	function renderError() {
		resultsContainer.replaceChildren();

		const error = document.createElement('div');

		error.className = 'ech-resource-error';
		error.textContent = 'Unable to load resources.';

		resultsContainer.appendChild(error);
	}

	function formatDate(value) {
		const date = new Date(value);

		if (Number.isNaN(date.getTime())) {
			return '';
		}

		return date.toLocaleDateString(undefined, {
			year: 'numeric',
			month: 'short',
			day: 'numeric',
		});
	}

	searchInput?.addEventListener('input', () => {
		clearTimeout(timeout);

		timeout = setTimeout(loadResources, 350);
	});

	departmentSelect?.addEventListener('change', loadResources);
});
