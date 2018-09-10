import Backbone from 'backbone';
import Marionette from 'backbone.marionette';
import RootView from './views/RootView';
import HeaderView from './views/HeaderView';

export default Marionette.Application.extend({
	initialize: function(options) {
		console.log('Initialize');
	},
	region: '#app',
	rootView: new RootView(),
	onStart: function() {
		this.showView(this.rootView);
	}

});