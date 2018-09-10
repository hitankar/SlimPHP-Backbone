import Marionette from 'backbone.marionette';
import template from '../../templates/appointment.jst';

export default Marionette.View.extend({
	template: template,
	tagName: 'li'
});