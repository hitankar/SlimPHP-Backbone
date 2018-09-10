import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'backbone.marionette';
import template from '../../templates/index-main.jst';

export default Marionette.View.extend({
	template: template,
	regions: {
		data: '#app-data'
	}
});