<?php

namespace EnterpriseContentHub\Security;

final class Capabilities {

	public const READ_RESOURCE = 'read_enterprise_resource';

	public const READ_PRIVATE_RESOURCES = 'read_private_enterprise_resources';

	public const EDIT_RESOURCE = 'edit_enterprise_resource';

	public const EDIT_RESOURCES = 'edit_enterprise_resources';

	public const EDIT_OTHERS_RESOURCES = 'edit_others_enterprise_resources';

	public const EDIT_PRIVATE_RESOURCES = 'edit_private_enterprise_resources';

	public const EDIT_PUBLISHED_RESOURCES = 'edit_published_enterprise_resources';

	public const PUBLISH_RESOURCES = 'publish_enterprise_resources';

	public const DELETE_RESOURCE = 'delete_enterprise_resource';

	public const DELETE_RESOURCES = 'delete_enterprise_resources';

	public const DELETE_OTHERS_RESOURCES = 'delete_others_enterprise_resources';

	public const DELETE_PRIVATE_RESOURCES = 'delete_private_enterprise_resources';

	public const DELETE_PUBLISHED_RESOURCES = 'delete_published_enterprise_resources';

	public const MANAGE_RESOURCES = 'manage_enterprise_resources';

	/**
	 * @return array<string>
	 */
	public static function all(): array {

		return array(
			self::READ_RESOURCE,
			self::READ_PRIVATE_RESOURCES,
			self::EDIT_RESOURCE,
			self::EDIT_RESOURCES,
			self::EDIT_OTHERS_RESOURCES,
			self::EDIT_PRIVATE_RESOURCES,
			self::EDIT_PUBLISHED_RESOURCES,
			self::PUBLISH_RESOURCES,
			self::DELETE_RESOURCE,
			self::DELETE_RESOURCES,
			self::DELETE_OTHERS_RESOURCES,
			self::DELETE_PRIVATE_RESOURCES,
			self::DELETE_PUBLISHED_RESOURCES,
			self::MANAGE_RESOURCES,
		);
	}
}
