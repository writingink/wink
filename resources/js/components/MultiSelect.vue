<script type="text/ecmascript">
    import _ from 'lodash';
    import moment from 'moment';

    export default {
        props: ['value', 'options', 'optionId', 'optionText', 'highlightPrivate'],

        data() {
            return {
                searchTerm: '',
                selectedOptionIndex: 0,
                focused: false,
                newOptions: []
            }
        },


        mounted() {
            this.$refs.input.style.width = this.value.length ? '25px' : '108px';
            this.$refs.input.placeholder = this.value.length ? '' : 'Add tags';
        },


        watch: {
            value(val) {
                this.$refs.input.style.width = this.value.length ? '25px' : '108px';
                this.$refs.input.placeholder = this.value.length ? '' : 'Add tags';
            },

            searchTerm(val) {
                var width = val.length * 12;

                this.$refs.input.style.width = width > 25 ? width + 'px' : '25px';
            },

            matches(val) {
                this.selectedOptionIndex = _.find(val, option => option[this.optionId] == 'addNew') ? 1 : 0;
            }
        },


        computed: {
            matches() {
                let options = _.union(this.options, this.newOptions);

                if (!this.searchTerm) {
                    return _.reject(options, option => {
                        return _.find(this.value, {id: option.id});
                    });
                } else {
                    var matches = _.reject(options, option => {
                        return _.find(this.value, {id: option.id}) ||
                            option[this.optionText].toLowerCase().indexOf(this.searchTerm.toLowerCase()) == -1;
                    });

                    let addNewOption = {};

                    addNewOption[this.optionText] = 'Add new';
                    addNewOption[this.optionId] = 'addNew';

                    matches.unshift(addNewOption);

                    return matches;
                }
            }
        },


        methods: {
            /**
             * Select the given option.
             */
            selectOption(option) {
                let values = this.value || [];

                if (_.includes(values, option.id)) return;

                if (option[this.optionId] == 'addNew') {
                    return this.addNewOption();
                }

                this.searchTerm = '';

                values.push(option);

                this.$emit('input', values);
            },


            /**
             * Remove the given option.
             */
            removeOption(option) {
                let values = this.value || [];

                values = _.reject(values, {id: option.id});

                this.$emit('input', values);
            },


            /**
             * Backspace was hit.
             */
            backspaceAction() {
                if (this.searchTerm) return;

                let values = this.value || [];

                values.pop();

                this.$emit('input', values);

                this.selectedOptionIndex = 0;
            },


            /**
             * Select the next option.
             */
            selectNextOption() {
                if (!this.matches.length) return;

                if (this.selectedOptionIndex + 1 == this.matches.length) return;

                this.selectedOptionIndex = this.selectedOptionIndex + 1;
            },


            /**
             * Select the previous option.
             */
            selectPreviousOption() {
                if (!this.matches.length) return;

                if (this.selectedOptionIndex === 0) return;

                this.selectedOptionIndex = this.selectedOptionIndex - 1;
            },

            /**
             * Add the selected option to the list.
             */
            addSelectedOption() {
                if (!this.matches[this.selectedOptionIndex]) {
                    return this.addNewOption();
                }

                this.selectOption(
                    this.matches[this.selectedOptionIndex]
                );

                this.selectedOptionIndex = 0;
            },


            /**
             * Add a brand new option.
             */
            addNewOption() {
                let values = this.value || [];
                let option = {};

                let existingOption = _.find(_.union(this.options, this.newOptions), option => {
                    return option.name.toLowerCase() == this.searchTerm.toLowerCase();
                });

                if (existingOption) {
                    this.searchTerm = '';

                    return;
                }

                option[this.optionText] = this.searchTerm;
                option[this.optionId] = _.uniqueId();

                values.push(option);

                this.newOptions.push(option);

                this.searchTerm = '';

                this.$emit('input', values);
            },


            /**
             * Activate the field.
             */
            activate() {
                this.focused = true;

                this.$refs.input.focus();
            },


            /**
             * Deactivate the field.
             */
            deactivate() {
                this.focused = false;
            }
        }
    }
</script>

<template>
    <div class="multiselect relative z-50"
         :class="{active: focused}"
         @click="activate"
         v-click-outside="deactivate">
        <div class="multiselect_options">
            <span
                  v-for="option in value"
                  v-on:click="removeOption(option)"
                  :class="'rounded cursor-pointer text-sm font-semibold px-1 mr-1 ' + ((highlightPrivate && option[optionText].indexOf('#')) === 0 ? 'border border-light bg-contrast text-light hover:border-red hover:text-red' : 'bg-light hover:bg-red text-contrast')">{{option[optionText]}}</span>

            <input type="text"
                   class="focus:outline-none bg-transparent text-text-color"
                   v-on:keydown.8="backspaceAction"
                   v-on:keydown.40="selectNextOption"
                   v-on:keydown.38="selectPreviousOption"
                   v-on:keydown.enter="addSelectedOption"
                   ref="input"
                   v-model="searchTerm">
        </div>

        <div class="multiselect_dropdown absolute w-full" v-show="focused">
            <button v-if="! matches.length" class="text-text-color">Add new tag...</button>
            <button v-for="(match, index) in matches"
                    v-on:click="selectOption(match)"
                    :class="{selected: selectedOptionIndex == index}"
                    :value="match[optionId]">{{match[optionText]}}
            </button>
        </div>
    </div>
</template>
