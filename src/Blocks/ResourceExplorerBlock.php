<?php

namespace EnterpriseContentHub\Blocks;

use EnterpriseContentHub\Application\ResourceQuery;
use EnterpriseContentHub\Services\ResourceService;

final class ResourceExplorerBlock {

	public function __construct(
		private readonly ResourceService $resource_service
	) {
	}

	public function register(): void {

		add_action(
			'init',
			array( $this, 'register_block' )
		);

		add_action(
			'wp_enqueue_scripts',
			array( $this, 'register_frontend_script' )
		);
	}

	public function register_block(): void {

		$block_path = ECH_PATH . 'build/resource-explorer';

		if ( ! file_exists( $block_path . '/block.json' ) ) {
			return;
		}

		register_block_type(
			$block_path,
			array(
				'render_callback' => array(
					$this,
					'render',
				),
			)
		);
	}

	public function register_frontend_script(): void {

		$script_path = ECH_PATH . 'build/resource-explorer/view.js';

		if ( ! file_exists( $script_path ) ) {
			return;
		}

		wp_register_script(
			'ech-resource-explorer-view',
			ECH_URL . 'build/resource-explorer/view.js',
			array(),
			(string) filemtime( $script_path ),
			true
		);
	}

	/**
	 * @param array<string, mixed> $attributes
	 */
	public function render( array $attributes ): string {

		wp_enqueue_script(
			'ech-resource-explorer-view'
		);

		$department = isset(
			$attributes['department']
		)
			? sanitize_title(
				(string) $attributes['department']
			)
			: '';

		$per_page = isset(
			$attributes['perPage']
		)
			? absint(
				$attributes['perPage']
			)
			: 6;

		$per_page = max(
			1,
			min(
				20,
				$per_page
			)
		);

		$show_search = ! empty(
			$attributes['showSearch']
		);

		$show_department = ! empty(
			$attributes['showDepartment']
		);

		$show_date = ! empty(
			$attributes['showDate']
		);

		$layout = (
			isset( $attributes['layout'] )
			&& 'list' === $attributes['layout']
		)
			? 'list'
			: 'grid';

		$result = $this->resource_service->search_resources(
			new ResourceQuery(
				department: $department,
				per_page: $per_page
			)
		);

		$departments = get_terms(
			array(
				'taxonomy'   => 'ech_department',
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $departments ) ) {
			$departments = array();
		}

		ob_start();
		?>

		<section
			class="ech-resource-explorer ech-layout-<?php echo esc_attr( $layout ); ?>"
			data-endpoint="<?php echo esc_url( rest_url( 'ech/v1/resources' ) ); ?>"
			data-per-page="<?php echo esc_attr( (string) $per_page ); ?>"
			data-show-date="<?php echo $show_date ? '1' : '0'; ?>"
		>

			<div class="ech-resource-toolbar">

				<?php if ( $show_search ) : ?>

					<label class="ech-resource-search-wrap">

						<span class="screen-reader-text">
							<?php
							echo esc_html__(
								'Search resources',
								'wp-enterprise-content-hub'
							);
							?>
						</span>

						<input
							type="search"
							class="ech-resource-search"
							placeholder="
							<?php
								echo esc_attr__(
									'Search resources...',
									'wp-enterprise-content-hub'
								);
							?>
											"
						/>

					</label>

				<?php endif; ?>

				<?php if ( $show_department ) : ?>

					<label class="ech-resource-department-wrap">

						<span class="screen-reader-text">
							<?php
							echo esc_html__(
								'Filter by department',
								'wp-enterprise-content-hub'
							);
							?>
						</span>

						<select class="ech-resource-department">

							<option value="">
								<?php
								echo esc_html__(
									'All Departments',
									'wp-enterprise-content-hub'
								);
								?>
							</option>

							<?php foreach ( $departments as $term ) : ?>

								<option
									value="<?php echo esc_attr( $term->slug ); ?>"
									<?php
									selected(
										$department,
										$term->slug
									);
									?>
								>
									<?php
									echo esc_html(
										$term->name
									);
									?>
								</option>

							<?php endforeach; ?>

						</select>

					</label>

				<?php endif; ?>

			</div>

			<div
				class="ech-resource-loading"
				hidden
			>
				<?php
				echo esc_html__(
					'Loading resources...',
					'wp-enterprise-content-hub'
				);
				?>
			</div>

			<div
				class="ech-resource-empty"
				<?php
				echo empty(
					$result['data']
				)
					? ''
					: 'hidden';
				?>
			>
				<?php
				echo esc_html__(
					'No resources found.',
					'wp-enterprise-content-hub'
				);
				?>
			</div>

			<div class="ech-resources">

				<?php foreach ( $result['data'] as $resource ) : ?>

					<article class="ech-resource-card">

						<div class="ech-resource-meta">

							<?php
							if (
								! empty(
									$resource->get_departments()
								)
							) :
								?>

								<span>
									<?php
									echo esc_html(
										implode(
											', ',
											$resource->get_departments()
										)
									);
									?>
								</span>

							<?php endif; ?>

							<?php if ( $show_date ) : ?>

								<span>
									<?php
									echo esc_html(
										wp_date(
											get_option(
												'date_format'
											),
											strtotime(
												$resource->get_updated_at()
											)
										)
									);
									?>
								</span>

							<?php endif; ?>

						</div>

						<h3 class="ech-resource-title">

							<a
								href="
								<?php
									echo esc_url(
										$resource->get_permalink()
									);
								?>
										"
							>
								<?php
								echo esc_html(
									$resource->get_title()
								);
								?>
							</a>

						</h3>

						<?php
						if (
							'' !==
							$resource->get_excerpt()
						) :
							?>

							<div class="ech-resource-excerpt">
								<?php
								echo wp_kses_post(
									wpautop(
										$resource->get_excerpt()
									)
								);
								?>
							</div>

						<?php endif; ?>

						<a
							class="ech-resource-link"
							href="
							<?php
								echo esc_url(
									$resource->get_permalink()
								);
							?>
									"
						>
							<?php
							echo esc_html__(
								'View Resource →',
								'wp-enterprise-content-hub'
							);
							?>
						</a>

					</article>

				<?php endforeach; ?>

			</div>

		</section>

		<?php

		return (string) ob_get_clean();
	}
}
