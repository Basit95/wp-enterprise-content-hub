import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

import {
	PanelBody,
	SelectControl,
	RangeControl,
	ToggleControl,
	Spinner,
	Notice,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';

export default function Edit({ attributes, setAttributes }) {
	const {
		department,
		perPage,
		showSearch,
		showDepartment,
		showDate,
		layout,
	} = attributes;

	const blockProps = useBlockProps({
		className: `ech-resource-explorer ech-layout-${layout}`,
	});

	const departments = useSelect((select) => {
		return select('core').getEntityRecords('taxonomy', 'ech_department', {
			per_page: 100,
			hide_empty: false,
		});
	}, []);

	const resources = useSelect(
		(select) => {
			const query = {
				per_page: perPage,
				status: 'publish',
			};

			if (department) {
				const selectedDepartment = departments?.find(
					(item) => item.slug === department
				);

				if (selectedDepartment) {
					query.ech_department = [selectedDepartment.id];
				}
			}

			return select('core').getEntityRecords(
				'postType',
				'ech_resource',
				query
			);
		},
		[department, perPage, departments]
	);

	const departmentOptions = [
		{
			label: 'All Departments',
			value: '',
		},
		...(departments || []).map((item) => ({
			label: item.name,
			value: item.slug,
		})),
	];

	return (
		<>
			<InspectorControls>
				<PanelBody
					title="Resource Explorer Settings"
					initialOpen={true}
				>
					<SelectControl
						label="Department"
						value={department}
						options={departmentOptions}
						onChange={(value) =>
							setAttributes({ department: value })
						}
					/>
					<RangeControl
						label="Number of resources"
						value={perPage}
						min={1}
						max={20}
						onChange={(value) => setAttributes({ perPage: value })}
					/>

					<SelectControl
						label="Layout"
						value={layout}
						options={[
							{
								label: 'Grid',
								value: 'grid',
							},
							{
								label: 'List',
								value: 'list',
							},
						]}
						onChange={(value) =>
							setAttributes({
								layout: value,
							})
						}
					/>

					<ToggleControl
						label="Show search"
						checked={showSearch}
						onChange={(value) =>
							setAttributes({
								showSearch: value,
							})
						}
					/>

					<ToggleControl
						label="Show department filter"
						checked={showDepartment}
						onChange={(value) =>
							setAttributes({
								showDepartment: value,
							})
						}
					/>

					<ToggleControl
						label="Show modified date"
						checked={showDate}
						onChange={(value) =>
							setAttributes({
								showDate: value,
							})
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<div className="ech-editor-header">
					<div>
						<span className="ech-editor-label">
							Enterprise Content Hub
						</span>

						<h3>Resource Explorer</h3>
					</div>

					<span className="ech-editor-layout">{layout}</span>
				</div>

				{showSearch && (
					<div className="ech-search-preview">
						Search resources...
					</div>
				)}

				{showDepartment && (
					<div className="ech-department-preview">
						Department: <strong>{department || 'All'}</strong>
					</div>
				)}

				{!resources && (
					<div className="ech-loading">
						<Spinner />

						<span>Loading resources...</span>
					</div>
				)}

				{resources && resources.length === 0 && (
					<Notice status="info" isDismissible={false}>
						No resources found for the current settings.
					</Notice>
				)}

				{resources && resources.length > 0 && (
					<div className="ech-resources-preview">
						{resources.map((resource) => (
							<article
								className="ech-resource-card"
								key={resource.id}
							>
								<span className="ech-resource-type">
									Resource
								</span>

								<h4>
									{resource.title?.rendered ||
										'Untitled resource'}
								</h4>

								{resource.excerpt?.rendered && (
									<div
										className="ech-resource-excerpt"
										dangerouslySetInnerHTML={{
											__html: resource.excerpt.rendered,
										}}
									/>
								)}

								{showDate && (
									<small>
										Updated:{' '}
										{new Date(
											resource.modified
										).toLocaleDateString()}
									</small>
								)}
							</article>
						))}
					</div>
				)}
			</div>
		</>
	);
}
