import Backbone from 'backbone';

export default Backbone.Model.extend({
	idAttribute: 'id',
	urlRoot: '//api.' + window.location.hostname + '/patients',
	defaults: {
		name: '',
		address: '',
		age: ''
	}
});