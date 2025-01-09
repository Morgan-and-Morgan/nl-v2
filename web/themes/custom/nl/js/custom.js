/**
 * external window dependencies:
 * - bootrap
 */

const $ = jQuery;

const SelectedVideo = Backbone.Model.extend({
    defauls: {
        id: null
    }
});


// the view containing the list of selectable video
const SelectVideoView = Backbone.View.extend({
    events: {
        'click .video': 'loadVideo'
    },

    loadVideo(evt) {
        this.model.set({id: $(evt.currentTarget).data('vidId')});
    }
});


const SelectedVideoPopup = Backbone.View.extend({
    template: null,

    initialize() {
      const templateElement = $('#video-template');

      // Check if #video-template exists
      if (templateElement.length === 0) {
        return;
      }

      // Compile the template
      this.template = _.template(templateElement.html());

      this.model.on('change:id', this.render, this);
      this.$el.on('hide.bs.modal', () => this.model.set({id: null}));
    },

    render() {
        let $videoContainer = this.$el.find('.modal-body');

        if (this.model.get('id')) {
            $videoContainer.html(this.template({
                video_id: this.model.get('id')
            }));

            this.$el.modal();
        } else {
            $videoContainer.html('');
        }
    }
});


let selectedVideo = new SelectedVideo();

new SelectVideoView({
    el: '.videos',
    model: selectedVideo
});

new SelectedVideoPopup({
    el: '.video-popup-container',
    model: selectedVideo
});
