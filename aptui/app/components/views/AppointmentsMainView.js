import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'backbone.marionette';
import AppointmentCollectionView from '../views/AppointmentCollectionView';
import template from '../../templates/appointments-main.jst';

export default Marionette.View.extend({
	template: template,
	patient_id: null,
	regions: {
		data: '#app-data'
	},
	onRender: function() {
		const patient_id = this.templateContext.patient_id;
		this.showChildView('data', new AppointmentCollectionView({patient_id: patient_id}));

		return this;
	}
});