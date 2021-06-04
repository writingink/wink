<script type="text/ecmascript">
    import FeaturedImageUploader from './FeaturedImageUploader';
    import SEOModal from './../../components/SEOModal';
    import MarkdownEditor from './../../components/MarkdownEditor';

    export default {
        components: {
            MarkdownEditor,
            'featured-image-uploader': FeaturedImageUploader,
            'seo-modal': SEOModal,
        },


        data() {
            return {
                ready: false,
                entry: null,
                currentTab: 'post',
                tags: [],
                authors: [],
                status: '',

                saveKeyboardShortcut: null,

                settingsModalShown: false,
                publishingModalShown: false,
                seoModalShown: false,

                id: this.$route.params.id || 'new',

                errors: [],

                postBodyWatcher: null,

                form: {
                    id: '',
                    title: 'Draft',
                    slug: '',
                    language: 'en',
                    excerpt: '',
                    tags: [],
                    author_id: '',
                    featured_image: '',
                    featured_image_caption: '',
                    body: '',
                    published: false,
                    markdown: ({null: null, 'markdown' : true, 'rich': false})[window.Wink.default_editor],
                    publish_date: '',
                    meta: {
                        meta_description: '',
                        opengraph_title: '',
                        opengraph_description: '',
                        opengraph_image: '',
                        opengraph_image_width: '',
                        opengraph_image_height: '',
                        twitter_title: '',
                        twitter_description: '',
                        twitter_image: '',
                    }
                }
            };
        },


        watch: {
            'form.slug'(val) {
                this.debouncer(() => {
                    this.form.slug = this.slugify(val);
                });
            },

            'form.featured_image'() {
                this.save();
            },


            'form.published'(val) {
                if (this.postBodyWatcher) {
                    this.postBodyWatcher();
                }

                if (!val) {
                    this.watchBodyChangesAndSave();
                }
            },

            '$route.params.id'() {
                this.id = this.$route.params.id;
            }
        },

        /**
         * Prepare the component.
         */
        mounted() {
            document.title = "Edit Post — Wink.";

            this.loadResources();

            this.http().get('/api/posts/' + this.id).then(response => {
                this.entry = _.cloneDeep(response.data.entry);

                this.fillForm(response.data.entry);

                this.ready = true;

                this.registerSaveKeyboardShortcut();
            }).catch(error => {
                this.ready = true;
            });
        },

        computed: {
            postPreviewLink() {
                return this.Wink.preview_path.replace('{postSlug}', this.form.slug);
            }
        },


        /**
         * Clean after the component is destroyed.
         */
        destroyed() {
            document.removeEventListener('keydown', this.saveKeyboardShortcut);
        },


        methods: {
            registerSaveKeyboardShortcut() {
                this.saveKeyboardShortcut = (event) => {
                    if ((event.ctrlKey || event.metaKey) && event.which == 83) {
                        event.preventDefault();

                        this.save();
                    }
                };

                document.addEventListener('keydown', this.saveKeyboardShortcut);
            },


            /**
             * Fill the form.
             */
            fillForm(data) {
                this.form.id = data.id;
                this.form.publish_date = data.publish_date;
                this.form.slug = 'draft-' + this.form.id;

                if (this.id != 'new') {
                    this.form.title = data.title;
                    this.form.slug = data.slug;
                    this.form.excerpt = data.excerpt;
                    this.form.body = data.body;
                    this.form.language = data.language;
                    this.form.published = data.published;
                    this.form.markdown = data.markdown;
                    this.form.tags = data.tags || '';
                    this.form.author_id = data.author_id || '';
                    this.form.featured_image = data.featured_image;
                    this.form.featured_image_caption = data.featured_image_caption;
                    this.form.meta = {
                        meta_description: data.meta.meta_description || '',
                        opengraph_title: data.meta.opengraph_title || '',
                        opengraph_description: data.meta.opengraph_description || '',
                        opengraph_image: data.meta.opengraph_image || '',
                        opengraph_image_width: data.meta.opengraph_image_width || '',
                        opengraph_image_height: data.meta.opengraph_image_height || '',
                        twitter_title: data.meta.twitter_title || '',
                        twitter_description: data.meta.twitter_description || '',
                        twitter_image: data.meta.twitter_image || '',
                    };
                }

                if (!this.form.published) {
                    this.watchBodyChangesAndSave();
                }
            },


            /**
             * Watch changes and save the post.
             */
            watchBodyChangesAndSave() {
                setTimeout(() => {
                    this.postBodyWatcher = this.$watch('form.body', _.debounce(() => this.save(), 1000), {deep: true});
                }, 1000);
            },


            /**
             * Load the resources needed for the screen.
             */
            loadResources() {
                this.http().get('/api/tags').then(response => {
                    this.tags = response.data.data;
                });

                this.http().get('/api/team').then(response => {
                    this.authors = response.data.data;

                    if (!this.form.author_id && this.authors) {
                        this.form.author_id = this.Wink.author.id;
                    }
                });
            },


            /**
             * Listen to changes in the post body.
             */
            updatePostBody(data) {
                this.form.body = data.body;
            },


            /**
             * Update the post title.
             */
            updateTitle(val) {
                this.form.title = val;
            },


            /**
             * Open the settings modal.
             */
            settingsModal() {
                this.settingsModalShown = true;
            },


            /**
             * Close the Settings modal.
             */
            closeSettingsModal() {
                this.settingsModalShown = false;

                this.save();
            },


            /**
             * Open the SEO & Social modal.
             */
            seoModal() {
                this.seoModalShown = true;
            },


            /**
             * Open the publishing modal.
             */
            publishingModal() {
                this.publishingModalShown = true;
            },


            /**
             * Open the featured image modal.
             */
            featuredImageModal() {
                this.$emit('openingFeaturedImageUploader');
            },


            /**
             * Handle the change event of featured images.
             */
            featuredImageChanged({url, caption}) {
                this.form.featured_image = url;
                this.form.featured_image_caption = caption;
            },


            /**
             * Handle the change event of featured images.
             */
            featuredImageRemoved() {
                this.form.featured_image = null;
                this.form.featured_image_caption = null;
            },


            /**
             * Close the SEO modal.
             */
            closeSeoModal({content}) {
                this.seoModalShown = false;
                this.form.meta = content;

                this.save();
            },


            /**
             * Delete the post.
             */
            deletePost() {
                this.alertConfirm("Are you sure you want to delete this post?", () => {
                    this.settingsModalShown = false;

                    this.http().delete('/api/posts/' + this.id, this.form).then(response => {
                        this.$router.push({name: 'posts'})
                    })
                });
            },


            /**
             * Publish the post.
             */
            publishPost() {
                this.form.published = true;

                this.save();

                this.publishingModalShown = false;

                this.notifySuccess('Post Published!', 2000);
            },


            /**
             * Un-publish the post.
             */
            unpublishPost() {
                this.form.published = false;

                this.save();

                this.publishingModalShown = false;

                this.notifySuccess('Post was converted to a draft!', 2000);
            },


            /**
             * Save the post.
             */
            save() {
                if (this.status) return;

                this.errors = [];
                this.status = 'Saving...';

                if (this.form.title != 'Draft' && (!this.form.slug || this.form.slug.startsWith('draft-'))) {
                    this.form.slug = this.slugify(this.form.title);
                }

                this.http().post('/api/posts/' + this.id, this.form).then(response => {
                    this.status = '';

                    if (this.id == 'new') {
                        this.$router.push({name: 'post-edit', params: {id: this.form.id}})
                    }
                }).catch(error => {
                    this.status = '';

                    this.errors = error.response.data.errors;

                    this.settingsModalShown = true;
                });
            },
        }
    }
</script>

<template>
    <div>
        <page-header>
            <div slot="left-side">
                <div v-if="ready && entry">
                    <span class="font-semibold" v-if="!status && form.published">Published</span>
                    <span class="font-semibold" v-if="!status && !form.published">Draft</span>
                    <span v-if="status">{{status}}</span>
                </div>
            </div>

            <div class="flex items-center" v-if="ready && entry" slot="right-side">
                <button class="py-1 px-2 btn-primary text-sm mr-6" @click="publishingModal" v-if="!form.published">Publish</button>
                <button class="py-1 px-2 btn-primary text-sm mr-6" @click="publishingModal" v-if="form.published">Update</button>

                <a :href="postPreviewLink" class="block focus:outline-none text-light hover:text-primary mr-6"
                   target="_blank"
                   title="Preview Post"
                   v-if="id != 'new'">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="w-4 h-4 fill-current">
                        <path d="M.2 10a11 11 0 0 1 19.6 0A11 11 0 0 1 .2 10zm9.8 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0-2a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                    </svg>
                </a>

                <dropdown class="relative">
                    <button slot="trigger" class="focus:outline-none text-light hover:text-primary h-8" title="Settings">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="w-4 h-4 fill-current mt-1">
                            <path d="M17 16v4h-2v-4h-2v-3h6v3h-2zM1 9h6v3H1V9zm6-4h6v3H7V5zM3 0h2v8H3V0zm12 0h2v12h-2V0zM9 0h2v4H9V0zM3 12h2v8H3v-8zm6-4h2v12H9V8z"/>
                        </svg>
                    </button>

                    <div slot="content" class="dropdown-content pin-r min-w-dropdown mt-1 text-sm py-2">
                        <a href="#" @click.prevent="settingsModal" class="no-underline text-text-color hover:text-primary w-full block py-2 px-4">
                            General Settings
                        </a>
                        <a href="#" @click.prevent="featuredImageModal" class="no-underline text-text-color hover:text-primary w-full block py-2 px-4">
                            Featured Image
                        </a>
                        <a href="#" @click.prevent="seoModal" class="no-underline text-text-color hover:text-primary w-full block py-2 px-4">
                            SEO & Social
                        </a>
                        <a href="#" @click.prevent="deletePost" class="no-underline text-red w-full block py-2 px-4" v-if="id != 'new'">Delete</a>
                    </div>
                </dropdown>
            </div>
        </page-header>

        <div class="container">
            <preloader v-if="!ready"></preloader>

            <h2 v-if="ready && !entry" class="text-center font-normal">
                404 — Post not found
            </h2>

            <div class="lg:w-3/4 mx-auto" v-if="ready && entry">
                <textarea-autosize
                    placeholder="Type something here..."
                    class="text-3xl font-semibold w-full focus:outline-none mb-10"
                    v-model="form.title"
                ></textarea-autosize>

                <div v-if="form.markdown == null">
                    <button class="w-full mb-5 hover:bg-lighter text-text-color block bg-very-light px-3 py-5 rounded" @click="form.markdown = false">
                        I want a rich text editor
                    </button>
                    <button class="w-full mb-5 hover:bg-lighter text-text-color block bg-very-light px-3 py-5 rounded" @click="form.markdown = true">
                        I will write markdown
                    </button>
                </div>
                <editor v-if="form.markdown == false" :post-id="id" v-model="form.body"></editor>
                <markdown-editor
                    v-model="form.body"
                    v-if="form.markdown == true">
                </markdown-editor>
            </div>
        </div>

        <!-- General Settings Modal -->
        <modal v-if="settingsModalShown" @close="closeSettingsModal">
            <div class="input-group pt-0">
                <label for="slug" class="input-label">Slug</label>
                <input type="text" class="input"
                       v-model="form.slug"
                       placeholder="Give me a slug"
                       id="slug">

                <form-errors :errors="errors.slug"></form-errors>
            </div>

            <div class="input-group">
                <label class="input-label">Language</label>
                <input type="text" class="input"
                       v-model="form.language"
                       placeholder="Give me a language"
                       id="language">

                <form-errors :errors="errors.language"></form-errors>
            </div>

            <div class="input-group">
                <label for="author_id" class="input-label">Author</label>
                <select name="author_id" class="input"
                        v-model="form.author_id"
                        id="author_id">
                    <option v-for="author in authors" :value="author.id">{{author.name}}</option>
                </select>
                <form-errors :errors="errors.author_id"></form-errors>
            </div>

            <div class="input-group">
                <label for="tag_ids" class="input-label mb-4">Tags</label>
                <multiselect :options="tags"
                             option-id="id"
                             v-model="form.tags"
                             option-text="name"
                ></multiselect>
                <form-errors :errors="errors.tags"></form-errors>
            </div>

            <div class="input-group">
                <label for="excerpt" class="input-label">Excerpt</label>
                <textarea class="input"
                          v-model="form.excerpt"
                          placeholder="What's this post about?"
                          id="excerpt"></textarea>

                <form-errors :errors="errors.excerpt"></form-errors>
            </div>

            <div class="mt-10">
                <button class="btn-sm btn-primary" @click="closeSettingsModal">Done</button>
            </div>
        </modal>

        <!-- Publishing Modal -->
        <modal v-if="publishingModalShown" @close="publishingModalShown = false">
            <div class="mb-10 text-red" v-if="form.title == 'Draft' || !form.slug || form.slug.startsWith('draft-')">
                Make sure your post has a friendly title and slug.
            </div>

            <div class="input-group pt-0">
                <label class="input-label">Publish Date (M/D/Y H:M) UTC</label>
                <date-time-picker v-model="form.publish_date"></date-time-picker>
                <form-errors :errors="errors.publish_date"></form-errors>
            </div>

            <div class="mt-10">
                <button class="btn-sm btn-primary" @click="publishPost" v-if="!form.published" v-loading="status">Publish this post</button>
                <button class="btn-sm btn-primary" @click="publishPost" v-if="form.published" v-loading="status">Update Post</button>
                <button class="btn-sm ml-1 btn-light" @click="unpublishPost" v-if="form.published" v-loading="status">Convert to draft</button>
                <button class="btn-sm ml-1 btn-light" @click="publishingModalShown = false">Cancel</button>
            </div>
        </modal>

        <!-- SEO & Social Modal -->
        <seo-modal v-if="seoModalShown"
                   :input="form.meta"
                   @close="closeSeoModal"></seo-modal>

        <!-- Featured Image Modal -->
        <featured-image-uploader :post-id="this.form.id"
                                 @changed="featuredImageChanged"
                                 @removed="featuredImageRemoved"
                                 :current-image-url="form.featured_image"
                                 :current-caption="form.featured_image_caption"></featured-image-uploader>
    </div>
</template>

<style scoped>
</style>
