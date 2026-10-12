import { registerBlockType } from '@wordpress/blocks';

import Edit from './edit';

import './style.scss';
import './editor.scss';

import metadata from './block.json';

registerBlockType(metadata.name, {
	edit: Edit,
	save() {
		return null;
	},
});
