export default {

    data() {

        return {
            items: [],
            loading: true,
            fieldTypes: null,
            page: 1,
            pages: 1,
            limit: 10,
            count: 0,
        }
    },

    props: {
        model: {
            type: Object,
            default: null
        },
        filter: {
            type: Object,
            default: null
        }
    },

    mounted() {

        App.utils.import('system:assets/js/settings.js').then(exp => {

            exp.FieldTypes.get().then(types => {
                this.fieldTypes = types;
                this.load();
            });
        });
    },

    template: /*html*/`
        <div>

            <div class="kiss-size-4 kiss-text-bold kiss-margin kiss-flex kiss-flex-middle">
                <icon class="kiss-margin-small-right kiss-size-3" size="larger">link</icon>
                <div class="kiss-flex-1">{{ t('Select model item') }}</div>
                <div class="kiss-badge kiss-badge-outline kiss-color-muted">{{ model.label || model.name }}</div>
            </div>

            <div class="kiss-dialog-overflow kiss-margin-large" expand="true">

                <app-loader v-if="!fieldTypes || loading"></app-loader>

                <div class="kiss-flex kiss-flex-middle kiss-flex-center kiss-align-center kiss-color-muted kiss-height-50vh" v-if="!loading && !items.length">
                    <div>
                        <kiss-svg class="kiss-margin-auto" :src="$base('content:assets/icons/collection.svg')" width="40" height="40"><canvas width="40" height="40"></canvas></kiss-svg>
                        <p class="kiss-size-large kiss-text-bold kiss-margin-small-top">{{ t('No items') }}</p>
                    </div>
                </div>

                <div class="table-scroll" v-if="!loading && items.length">
                    <table class="kiss-table animated fadeIn">
                        <thead>
                            <tr>
                                <th fixed="left" width="50">ID</th>
                                <th width="20">State</th>
                                <th v-for="field in model.fields">{{ field.label || field.name}}</th>
                                <th width="120"><?=t('Modified')?></th>
                                <th fixed="right" width="20"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items">
                                <td fixed="left"><a class="kiss-badge kiss-link-muted" :title="item._id">...{{ item._id.substr(-5) }}</a></td>
                                <td class="kiss-align-center"><icon :class="{'kiss-color-success': item._state === 1, 'kiss-color-danger': !item._state}">trip_origin</icon></td>
                                <td v-for="field in model.fields">
                                    <span class="kiss-badge kiss-badge-outline kiss-color-muted" v-if="item[field.name] == null">n/a</span>
                                    <div class="kiss-text-truncate" v-else-if="fieldTypes[field.type] && fieldTypes[field.type].render" v-html="fieldTypes[field.type].render(item[field.name], field, 'table-cell')"></div>
                                    <div class="kiss-text-truncate" v-else>
                                        <span class="kiss-badge kiss-badge-outline" v-if="Array.isArray(item[field.name])">{{ item[field.name].length }}</span>
                                        <span class="kiss-badge kiss-badge-outline" v-else-if="typeof(item[field.name]) == 'object'">Object</span>
                                        <span v-else>{{ item[field.name] }}</span>
                                    </div>
                                </td>
                                <td><span class="kiss-flex kiss-badge kiss-badge-outline kiss-color-primary">{{ (new Date(item._modified * 1000).toLocaleString()) }}</span></td>
                                <td fixed="right"><a class="kiss-size-large" @click="pick(item)"><icon>link</icon></a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="kiss-flex kiss-flex-right kiss-margin-top">

                <div class="kiss-flex kiss-flex-middle kiss-flex-1" v-if="!loading && count">
                    <div class="kiss-size-small">{{ count }} {{count == 1 ? t('Item') : t('Items') }}</div>
                    <div class="kiss-margin-small-left kiss-overlay-input">
                        <span class="kiss-badge kiss-badge-outline kiss-color-muted">{{ page }} / {{pages}}</span>
                        <select v-model="page" @change="load(page)" v-if="pages > 1"><option v-for="p in pages" :value="p">{{ p }}</option></select>
                    </div>
                    <div class="kiss-margin-small-left kiss-size-small">
                        <a class="kiss-margin-small-right" v-if="(page - 1) >= 1" @click="load(page - 1)">{{ t('Previous') }}</a>
                        <a v-if="(page + 1) <= pages" @click="load(page + 1)">{{ t('Next') }}</a>
                    </div>
                </div>

                <button class="kiss-button kiss-button-primary" @click="$close()">
                    {{ t('Cancel') }}
                </button>
            </div>
        </div>
    `,

    methods: {

        load(page = 1) {

            let options = {
                limit: this.limit,
                skip: (page - 1) * this.limit,
            };

            if (this.filter) {
                options.filter = this.filter;
            }

            this.loading = true;
            this.selected = [];

            this.$request(`/content/collection/find/${this.model.name}`, {options}).then(rsp => {
                this.items = rsp.items;
                this.page = rsp.page;
                this.pages = rsp.pages;
                this.count = rsp.count;

                this.loading = false;
            })
        },

        pick(item) {
            this.$call('pickItem', item);
            this.$close();
        }
    }
}