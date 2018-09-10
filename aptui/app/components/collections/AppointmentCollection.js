import Backbone from 'backbone';
import AppointmentModel from '../models/AppointmentModel';

export default Backbone.Collection.extend({
	model: AppointmentModel,
	url: '//api.' + window.location.hostname + '/appointments'
});