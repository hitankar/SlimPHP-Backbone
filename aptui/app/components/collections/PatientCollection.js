import Backbone from 'backbone';
import PatientModel from '../models/PatientModel';

export default Backbone.Collection.extend({
	model: PatientModel,
	url: '//api.' + window.location.hostname + '/patients'
});