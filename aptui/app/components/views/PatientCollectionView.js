import _ from 'underscore';
import $ from 'jquery';
import Marionette from 'backbone.marionette';
import PatientView from './PatientView';
import PatientModel from '../models/PatientModel';
import PatientCollection from '../collections/PatientCollection';

export default Marionette.CollectionView.extend({
	tagName: 'main',
	className: 'row',
	childView: PatientView,
	collection: new PatientCollection(),
	initialize: function() {
		this.$el.html('');
		this.listenTo(this.collection, 'change', this.render);
		this.collection.fetch({
			success: function(response) {
				_.each(response.toJSON(), (patient) => {
					console.log('Loaded patient Template ' + patient.id);
				});
			},
			error: function(e) {
				console.log('Error ' + e);
			}
		});
	},
	events: {
		'click .btn-delete-patient' : 'deletePatient'
	},
	deletePatient: function(e, a) {
		e.preventDefault();
		const $elem = $(e.currentTarget);
		this.collection.remove($elem.data('id'));
	},
	onDomRefresh: function() {
		const view = this;
		$('.modal').modal({
			ready: function(modal, trigger) {
				$('#name').focus();
			}
		});
		let typingTimer = null;
		const doneTypingInterval = 900;
		const $input = $('#patient_search');

		$input.on('keyup', (event) => {
			clearTimeout(typingTimer);
			const $elem = $input;
			typingTimer = setTimeout(() => {
				let search = $elem.val();
				if (search.length === 0) {
					search = '';
				} else if (search.length < 2) {
					return false;
				}
				view.$el.html('');
				view.collection.fetch({
					data: {'search': encodeURIComponent(search)},
					success: function(response) {
						if (response.length === 0) {
							view.$el.html('<div class="center">No Patient information found.</div>');
						}
						_.each(response.toJSON(), (patient) => {
							console.log('Loaded patient Template ' + patient.id);
						});
					},
					error: function(e) {
						console.log('Error ' + e);
					}
				});
				view.render();
				event.preventDefault();

				return false;
			}, doneTypingInterval);
		});


		$input.on('keydown', (event) => {
			clearTimeout(typingTimer);
		});

		const $form = $('form#newPatient');
		$form.on('submit', (e) => {
			const name = $('#name', $form).val();
			const age = $('#age', $form).val();
			const address = $('#address', $form).val();
			view.collection.create({
				name: name,
				age: age,
				address: address
			}, {
				wait: true,
				success : function(resp) {
					console.log('success callback');
					$('#name').val('');
					$('#age').val('');
					$('#address').val('');
				}
			});
			e.preventDefault();
		});
	}
});