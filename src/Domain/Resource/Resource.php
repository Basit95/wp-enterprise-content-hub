<?php

namespace EnterpriseContentHub\Domain\Resource;

final class Resource {

	/**
	 * @param array<int, string> $departments
	 */
	public function __construct(
		private readonly int $id,
		private readonly string $title,
		private readonly string $excerpt,
		private readonly string $content,
		private readonly string $status,
		private readonly string $permalink,
		private readonly array $departments,
		private readonly string $updated_at
	) {
	}

	public function get_id(): int {
		return $this->id;
	}

	public function get_title(): string {
		return $this->title;
	}

	public function get_excerpt(): string {
		return $this->excerpt;
	}

	public function get_content(): string {
		return $this->content;
	}

	public function get_status(): string {
		return $this->status;
	}

	public function get_permalink(): string {
		return $this->permalink;
	}

	/**
	 * @return array<int, string>
	 */
	public function get_departments(): array {
		return $this->departments;
	}

	public function get_updated_at(): string {
		return $this->updated_at;
	}

	/**
	 * Convert resource into API-friendly array.
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {

		return array(
			'id'          => $this->id,
			'title'       => $this->title,
			'excerpt'     => $this->excerpt,
			'content'     => $this->content,
			'status'      => $this->status,
			'permalink'   => $this->permalink,
			'departments' => $this->departments,
			'updated_at'  => $this->updated_at,
		);
	}
}
