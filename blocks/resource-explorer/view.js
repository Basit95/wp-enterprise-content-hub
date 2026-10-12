const explorers = document.querySelectorAll('.ech-resource-explorer');

explorers.forEach((explorer) => {
	const searchInput = explorer.querySelector('.ech-resource-search');
	const departmentSelect = explorer.querySelector('.ech-resource-department');
	const resultsContainer = explorer.querySelector('.ech-resources');
	const loadingElement = explorer.querySelector('.ech-resource-loading');
	const emptyElement = explorer.querySelector('.ech-resource-empty');
	const perPage = explorer.dataset.perPage || 6;
	const showDate = explorer.dataset.showDate === '1';

	let timeout = null;

	async function loadResources() {
		const params = new URLSearchParams();

		params.set('per_page', perPage);
		params.set('page', '1');

		if (searchInput && searchInput.value.trim()) {
			params.set('search', searchInput.value.trim());
		}
		if (departmentSelect && departmentSelect.value) {
			params.set('department', departmentSelect.value);
		}

		loadingElement?.removeAttribute('hidden');
		emptyElement?.setAttribute('hidden', '');

		try {
			const response = await fetch(
				`/wp-json/ech/v1/resources?${params.toString()}`
			);

			if (!response.ok) {
				throw new Error('Unable to load resources.');
			}

			const responseData = await response.json();

			renderResources(responseData.data || []);
		} catch (error) {
			resultsContainer.innerHTML = `
                <div class="ech-resource-error">
                    Unable to load resources.
                </div>
            `;
		} finally {
			loadingElement?.setAttribute('hidden', '');
		}
	}

	function renderResources(resources) {
		resultsContainer.innerHTML = '';

		if (!resources.length) {
			emptyElement?.removeAttribute('hidden');

			return;
		}

		emptyElement?.setAttribute('hidden', '');

		resources.forEach((resource) => {
			const article = document.createElement('article');

			article.className = 'ech-resource-card';

			const departments = resource.departments || [];

			article.innerHTML = `
                <div class="ech-resource-meta">
                    ${
						departments.length
							? `<span>${escapeHtml(
									departments.join(', ')
								)}</span>`
							: ''
					}

                    ${
						showDate && resource.updated_at
							? `<span>${formatDate(resource.updated_at)}</span>`
							: ''
					}
                </div>

                <h3 class="ech-resource-title">
                    <a href="${escapeAttribute(resource.permalink)}">
                        ${escapeHtml(resource.title)}
                    </a>
                </h3>

                ${
					resource.excerpt
						? `
                            <div class="ech-resource-excerpt">
                                ${resource.excerpt}
                            </div>
                        `
						: ''
				}

                <a
                    class="ech-resource-link"
                    href="${escapeAttribute(resource.permalink)}"
                >
                    View Resource →
                </a>
            `;

			resultsContainer.appendChild(article);
		});
	}

	function escapeHtml(value) {
		const element = document.createElement('div');

		element.textContent = value || '';

		return element.innerHTML;
	}

	function escapeAttribute(value) {
		return String(value || '')
			.replace(/&/g, '&amp;')
			.replace(/"/g, '&quot;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;');
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
