import _ from 'underscore';
import Backbone from 'backbone';
import Marionette from 'backbone.marionette';
import PatientCollectionView from '../views/PatientCollectionView';
import template from '../../templates/patients-main.jst';

export default Marionette.View.extend({
	template: template,
	regions: {
		data: '#app-data'
	},
	onRender: function() {
		this.showChildView('data', new PatientCollectionView());
	}
});