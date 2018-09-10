import _ from 'underscore';
import $ from 'jquery';
import Backbone from 'backbone';
import Marionette from 'backbone.marionette';
import AppointmentView from './AppointmentView';
import AppointmentCollection from '../collections/AppointmentCollection';

export default Marionette.CollectionView.extend({
	childView: AppointmentView,
	collection: new AppointmentCollection(),
	tagName: 'ul',
	className: 'collapsible popout',
	attributes: {'data-collapsible': 'accordion'},
	model: new Backbone.Model({sorted: 'ASC'}),
	getViewComparator: function() {
		return -this.model.get('id');
	},
	attachHtml: function(collectionView, itemView) {
		collectionView.$el.prepend(itemView.el);
	},
	initialize: function() {
		this.collection.reset();
		this.$el.html('');
		this.listenTo(this.collection, 'change', this.render);
		this.listenTo(this.collection, 'reset', this.render);
		const self = this;
		this.collection.fetch({
			data: {patient: self.options.patient_id},
			success: function(response) {
				if (response.length === 0) {
					self.$el.html('<div class="center">No Appointments found.</div>');
				}
				_.each(response.toJSON(), (appointment) => {
					console.log('Loaded Appointments ' + appointment.id);
				});
			},
			error: function(e) {
				console.log('Error ' + e.toJSON);
				self.$el.html('<div class="center">No Appointments found.</div>');
			}
		});
	},
	onDomRefresh: function() {
		$('.collapsible').collapsible();
		$('.modal').modal();
		$('#symptoms').material_chip({
			placeholder: 'Enter a symptom'
		});
		$('#drugs').material_chip({
			placeholder: 'Enter a drug'
		});
		const view = this;
		const $form = $('form#newAppointment');
		$('#repeat', $form).on('click', (event) => {
			event.preventDefault();
			const last_appointment = view.collection.at(view.collection.length - 1);
			const symptoms = [];
			_.each(last_appointment.get('discovered_symptoms'), (symptom) => {
				symptoms.push({
					tag: symptom.name
				});
			});
			const drugs = [];
			_.each(last_appointment.get('administered_drugs'), (drug) => {
				drugs.push({
					tag: drug.name
				});
			});
			$('#symptoms', $form).material_chip({data: symptoms});
			$('#drugs', $form).material_chip({data: drugs});
		});
		$form.on('submit', (e) => {
			const symptoms_object = $('#symptoms', $form).material_chip('data');
			const drugs_object = $('#drugs', $form).material_chip('data');
			const symptoms = $.map(symptoms_object, (value, index) => {
				return [value];
			});
			const drugs = $.map(drugs_object, (value, index) => {
				return [value];
			});
			view.collection.create({
				discovered_symptoms: symptoms,
				administered_drugs: drugs,
				next_appointment: $('#next_appointment', $form).val(),
				patient: $('#patient_id', $form).val(),
				note: $('#note', $form).val()
			}, {
				wait: true,
				success : function(resp) {
					console.log('success callback');
					$('#symptoms', $form).material_chip('');
					$('#drugs', $form).material_chip('');
					$('#next_appointment', $form).val('');
					$('#note', $form).val('');
				}
			});

			e.preventDefault();
		});
		// setTimeout(() => {
		// 	$('.collapsible').collapsible('open', 0);
		// }, 1000);
		$('.datepicker').pickadate({
			selectMonths: true,
			selectYears: 1,
			format: 'yyyy-mm-dd'
		});

	},
	onRenderChildren: function() {
		$('.collapsible').collapsible('open', 0);
	},
	onAddChild: function() {
		$('.collapsible').collapsible('open', 0);
	}
});