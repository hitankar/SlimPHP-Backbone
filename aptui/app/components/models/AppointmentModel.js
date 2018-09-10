import Backbone from 'backbone';

export default Backbone.Model.extend({
	idAttribute: 'id',
	defaults: {
		next_appointment: '',
		discovered_symptoms: '',
		administered_drugs: '',
		patient: '',
		note: ''
	}
});