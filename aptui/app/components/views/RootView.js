import _ from 'underscore';
import $ from 'jquery';
import Marionette from 'backbone.marionette';
import template from '../../templates/layout.jst';

export default Marionette.View.extend({
	template: template,
	el: '#app',
	regions: {
		header: 'header[role="banner"]',
		main: '#main',
		footer: 'footer[role="content-info"]'
	},
	onDomRefresh: function() {
		$('.button-collapse').sideNav();
	}
});