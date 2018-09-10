import Marionette from 'backbone.marionette';
import template from '../../templates/patient.jst';

export default Marionette.View.extend({
	template: template,
	tagName: 'div',
	className: 'col s12 m6',
	render: function() {
		this.$el.html(this.template(this.model.toJSON()));

		return this;
	}

});