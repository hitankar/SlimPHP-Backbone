import 'materialize-css/sass/materialize.scss';
import jQuery from 'jquery';
import './styles/application.scss';
import 'materialize-css/bin/materialize';

import Backbone from 'backbone';
import Application from 'components/App';
import AppRouter from 'components/routers/AppRouter';
import AppController from 'components/controllers/AppController';

(function() {
  const proxiedSync = Backbone.sync;
  Backbone.sync = function(method, model, options) {
	options || (options = {});
	if (!options.crossDomain) {
		options.crossDomain = true;
	}
	if (!options.xhrFields) {
		options.xhrFields = {withCredentials:true};
	}

	return proxiedSync(method, model, options);
  };
})();

(($) => {
	window.$ = $;
	$.ajaxPrefilter((options, originalOptions, jqXHR) => {
		options.crossDomain = {
			crossDomain: true
		};
		options.xhrFields = {
			withCredentials: true
		};
	});
})(jQuery);

document.addEventListener('DOMContentLoaded', () => {
	const App = new Application();
	App.on('start', () => {

		if (Backbone.history) {
			Backbone.history.start();
		}
	});

	const isMobile = function() {
		const ua = (navigator.userAgent || navigator.vendor || window.opera, window, window.document);

		return (/iPhone|iPod|iPad|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
	};
	App.mobile = isMobile();

	App.appRouter = new AppRouter({
		controller: AppController,
		appRoutes: {
			'': 'index',
			'patients': 'patients',
			'patients/:patientId/appointments': 'appointments'
		}
	});

	App.start();

	return App;
});

jQuery(window);
