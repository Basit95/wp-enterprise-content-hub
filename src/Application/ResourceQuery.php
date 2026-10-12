<?php

namespace EnterpriseContentHub\Application;

final class ResourceQuery {

	public function __construct(
		private readonly string $search = '',
		private readonly string $department = '',
		private readonly int $page = 1,
		private readonly int $per_page = 10,
		private readonly string $status = 'publish'
	) {
	}

	public function get_search(): string {

		return $this->search;
	}

	public function get_department(): string {

		return $this->department;
	}

	public function get_page(): int {

		return max( 1, $this->page );
	}

	public function get_per_page(): int {

		return min(
			50,
			max( 1, $this->per_page )
		);
	}

	public function get_status(): string {

		return $this->status;
	}
}
