import Marionette from 'backbone.marionette';
import PatientCollectionView from '../views/PatientCollectionView';
import AppointmentCollectionView from '../views/AppointmentCollectionView';
import AppointmentCollection from '../collections/AppointmentCollection';
import HeaderView from '../views/HeaderView';
import FooterView from '../views/FooterView';
import RootView from '../views/RootView';
import IndexMainView from '../views/IndexMainView';
import PatientsMainView from '../views/PatientsMainView';
import AppointmentsMainView from '../views/AppointmentsMainView';

export default {
	rootView: new RootView(),
	// gets mapped to in AppRouter's appRoutes
	index: function () {
		window.location.replace('#/patients');
	},

	patients: function () {
		this.rootView.render();
		this.rootView.showChildView('header', new HeaderView());
		this.rootView.showChildView('main', new PatientsMainView());
		this.rootView.showChildView('footer', new FooterView());
	},

	appointments: function (patientId) {
		this.rootView.render();
		this.rootView.showChildView('header', new HeaderView());
		this.rootView.showChildView('main', new AppointmentsMainView({
			templateContext: {patient_id: patientId}
		}));
		this.rootView.showChildView('footer', new FooterView());
	}
};