import Marionette from 'backbone.marionette';
import AppController from 'components/controllers/AppController';

export default Marionette.AppRouter.extend({

	controller: AppController,
   // "index" must be a method in AppRouter's controller
	appRoutes: {
		'': 'index'
   }
});