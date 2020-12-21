<script type="text/ecmascript">
    import _ from 'lodash';

    export default {
        props: ['postId', 'currentImageUrl', 'currentCaption'],

        data() {
            return {
                imageUrl: '',
                caption: '',
                imagePickerKey: '',
                uploadProgress: 0,
                uploading: false,

                modalShown: false,
            }
        },


        mounted() {
            this.$parent.$on('openingFeaturedImageUploader', data => {
                this.imageUrl = this.currentImageUrl;
                this.caption = this.currentCaption;

                this.modalShown = true;
            });
        },


        methods: {
            /**
             * Save the image.
             */
            saveImage() {
                this.$emit('changed', {url: this.imageUrl, caption: this.caption});

                this.close();
            },

            /**
             *  Remove image
             */
            removeImage() {
                this.imageUrl = null;
                this.$emit('removed');
            },


            /**
             * Close the modal.
             */
            close() {
                this.imagePickerKey = _.uniqueId();

                this.modalShown = false;
            },


            /**
             * Update the selected image.
             */
            updateImage({url, caption}) {
                this.imageUrl = url;
                this.caption = caption;

                this.uploading = false;
            },


            /**
             * Update the upload progress.
             */
            updateProgress({progress}) {
                this.uploadProgress = progress;
            }
        }
    }
</script>

<template>
    <modal v-if="modalShown" @close="close">
        <h2 class="font-semibold mb-5">Featured Image</h2>

        <preloader v-if="uploading"></preloader>

        <div v-if="imageUrl && !uploading">
            <div class="relative">
                <button @click="removeImage"
                        class="btn-sm bg-red absolute pin-t pin-r border-black rounded mr-1 mt-1 h-6 w-8 bg-very-light">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img :src="imageUrl" class="max-w-full">
            </div>

            <div class="input-group">
                <label class="input-label">Caption</label>
                <textarea rows="2" v-model="caption" ref="caption" class="input" placeholder="Add caption to the image"></textarea>
            </div>
        </div>

        <image-picker v-if="!imageUrl"
                      :key="imagePickerKey"
                      class="mt-5"
                      @changed="updateImage"
                      @progressing="updateProgress"
                      @uploading="uploading = true"></image-picker>

        <button class="btn-sm btn-primary mt-10" @click="saveImage">Save Image</button>
        <button class="btn-sm btn-light mt-10" @click="close">Cancel</button>
    </modal>
</template>

<style>

</style>
