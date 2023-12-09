<template>
    <div class="d-flex justify-content-between my-5">
        <h2 class="fs-2 fw-semibold my-2">
            {{ props.title }}
        </h2>
        <div class="d-flex gap-2 my-1">
            <div class="btn-group">
                <button type="button" class="btn dropdown-toggle text-muted btn-light" data-bs-toggle="dropdown" aria-expanded="false">
                    Filters
                </button>
                <div class="dropdown-menu">
                    <div class="px-3 py-1 text-center">
                        <div class="fs-5 text-muted fw-bold">Filters</div>
                    </div>
                    <hr/>
                    <div class="px-3 py-1">
                        <div class="d-flex align-items-center gap-1 mb-3">
                            <label>Priority:</label>
                            <div class="min-pw-150">
                                <select id="filter-priority" v-model="filter.priority_id" @change="applyFilter"></select>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1 mb-3">
                            <label>Title:</label>
                            <input type="text" class="form-control shadow-none" v-model="filter.title" @keyup="applyFilter" />
                        </div>
                        <div class="d-flex align-items-center gap-1 mb-3">
                            <label>Due:</label>
                            <div class="min-pw-150">
                                <VueDatePicker v-model="filter.due_at" :format="(dates) => dates && formatDateRange(...dates)" auto-apply range input-class-name="shadow-none" @update:model-value="applyFilter" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn-group">
                <button type="button" class="btn dropdown-toggle text-muted btn-light" data-bs-toggle="dropdown" aria-expanded="false">
                    Sorting
                </button>
                <div class="dropdown-menu">
                    <div class="px-3 py-1 text-center">
                        <div class="fs-5 text-muted fw-bold">Sorting</div>
                    </div>
                    <hr/>
                    <div class="px-3 py-1">
                        <div class="d-flex align-items-center gap-1 mb-3">
                            <label>By:</label>
                            <div class="min-pw-150">
                                <select class="form-control shadow-none" v-model="sort.column" @change="applyFilter">
                                    <option :value="dtColumns.findIndex(c => c.data === column.data)" v-for="(column) in dtColumns.filter(column => column.orderable === true)">{{ column.title }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1 mb-3">
                            <label>Order:</label>
                            <select class="form-control shadow-none" v-model="sort.dir" @change="applyFilter">
                                <option value="asc">Ascending</option>
                                <option value="desc">Descending</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" @click="editTask()">Add Task</button>
        </div>
    </div>
    
    <DataTable :options="dtSettings(props.completed, props.archived)" ref="paginator" v-if="priorities.length > 0" />

    <div v-if="tasks.length > 0">
        <Teleport to="#content">
            <div class="row my-5">
                <div class="col-md-6 col-xl-4" v-for="(task) in tasks">
                    <div class="card mb-4" :class="`border-${task.priority?.color}`">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex gap-1">
                                    <Iconable :icon="'flag-fill'" :color="task.priority?.color" v-if="task.priority" />
                                    <div><span class="badge fw-medium text-white rounded-pill bg-success" v-if="task.completed_at">Completed</span></div>
                                    <div><span class="badge fw-medium text-white rounded-pill bg-secondary" v-if="task.archived_at">Archived</span></div>
                                </div>
                                <div>
                                    <div class="dropdown">
                                        <button type="button" class="btn border-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li v-if="!task.archived_at">
                                                <a class="dropdown-item" @click="updateStatus('archived', task, true)">Archive</a>
                                            </li>
                                            <li v-if="task.archived_at">
                                                <a class="dropdown-item" @click="updateStatus('archived', task, false)">Restore</a>
                                            </li>
                                            <li v-if="!task.deleted_at">
                                                <a class="dropdown-item" @click="updateStatus('deleted', task, true)">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mb-3 cursor-pointer">
                                <label class="d-flex align-items-start" :for="`completed-${task.id}`">
                                    <div class="form-check">
                                        <input class="form-check-input border-2 p-2 rounded-circle shadow-none cursor-pointer" type="checkbox"
                                            :class="'border-' + task.priority?.color"
                                            v-model="task.completed_at"
                                            @change="(event) => updateStatus('completed', task, event.target.checked)"
                                            :id="`completed-${task.id}`" />
                                    </div>
                                </label>
                                <div class="w-100" @click="editTask(task)">
                                    <div class="fw-semibold opacity-75 mb-2">{{ task.title }}</div>
                                    <div class="text-muted">{{ task.plain_text_description }}</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <div>
                                    <div class="d-flex align-items-center" v-if="task.due_at">
                                        <i class="bi bi-calendar4-event"></i>
                                        <span class="ms-2">{{ formatDate(task.due_at) }}</span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="border rounded d-flex align-items-center py-1 px-2" v-if="task.media_count > 0">
                                        <i class="bi bi-paperclip"></i>
                                        <span class="ms-1">{{ task.media_count }}</span>
                                    </div>
                                    <div class="border d-flex align-items-center rounded py-1 px-2" :class="{'ms-1':task.tags_count > 0}" v-if="task.tags_count > 0">
                                        <i class="bi bi-tag"></i>
                                        <span class="ms-1">{{ task.tags_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>

    <div v-if="paginator?.hasOwnProperty('dt') && tasks.length == 0">
        <Teleport to="#content">
            <div>Nothing.</div>
        </Teleport>
    </div>

    <div class="modal fade" tabindex="-1" id="task-modal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" v-if="task">
                <div class="modal-header d-flex">
                    <div class="flex-grow-1 d-flex gap-1">
                        <div><span class="badge fw-medium text-white rounded-pill bg-success" v-if="task.completed_at">Completed</span></div>
                        <div><span class="badge fw-medium text-white rounded-pill bg-secondary" v-if="task.archived_at">Archived</span></div>
                    </div>
                    <div class="btn-group" v-if="task.id">
                        <button type="button" class="btn dropdown-toggle border-0 text-muted" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li v-if="!task.archived_at">
                                <a class="dropdown-item" @click="updateStatus('archived', task, true)">Archive</a>
                            </li>
                            <li v-if="task.archived_at">
                                <a class="dropdown-item" @click="updateStatus('archived', task, false)">Restore</a>
                            </li>
                            <li v-if="!task.deleted_at">
                                <a class="dropdown-item" @click="updateStatus('deleted', task, true)">Delete</a>
                            </li>
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <form id="taskForm" @submit.prevent>
                        <div class="row g-0 vh-75">
                            <div class="col-8">
                                <div class="container-fluid mt-3">
                                    <div class="d-flex">
                                        <div class="align-items-start mt-1">
                                            <div class="form-check">
                                                <input class="form-check-input border-2 p-2 rounded-circle shadow-none" type="checkbox"
                                                    :class="'border-' + task.priority?.color"
                                                    :checked="task.completed_at"
                                                    v-model="task.completed_at"
                                                    @change="(event) => updateStatus('completed', task, event.target.checked)" />
                                            </div>
                                        </div>
                                        <div class="w-100">
                                            <input class="form-control border-0 shadow-none fw-semibold opacity-75" placeholder="Task title.." v-model="task.title" required />                                    
                                            <div class="d-inline">
                                                <input id="description-validator" class="opacity-0 vh-0 w-50 position-absolute ms-5 mt-4" required />
                                                <textarea id="description-input"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-success" @click="saveEditor">Save</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 bg-light rounded">
                                <div class="container-fluid mt-3">
                                    <div class="mb-3">
                                        <p class="h6">Due date</p>
                                        <VueDatePicker v-model="task.due_at" :format="formatDate" auto-apply input-class-name="border-0 shadow-none bg-transparent" />
                                    </div>
                                    <div class="mb-3">
                                        <p class="h6">Priority</p>
                                        <select id="priority-input" v-model="task.priority_id"></select>
                                    </div>
                                    <div class="mb-3">
                                        <p class="h6">Tags</p>
                                        <!-- <input id="tags-input" autocomplete="off" placeholder="Add tags.." v-model="task.tags"> -->
                                        <select id="tags-input" placeholder="Add tags.." multiple v-model="task.tags"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/authStore';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import { Modal } from 'bootstrap';
import FroalaEditor from 'froala-editor/js/froala_editor.pkgd.js';
import 'froala-editor/js/plugins.pkgd.min.js';
import api from '@/api';
import TomSelect from 'tom-select/src/tom-select.complete';
import { ref, onMounted, createApp } from 'vue';
import Iconable from '@/views/components/Iconable.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import { DateTime } from "luxon";

const props = defineProps(['completed', 'archived', 'title']);

const dateFormat = 'yyyy-LL-dd';

function toLuxon(date) {
    const parser = date.constructor.name == 'Date' ? 'fromJSDate' : 'fromISO';
    return DateTime[parser](date);
}

function formatDate(date) {
    return date ? toLuxon(date).toFormat(dateFormat) : null;
}

function formatDateRange(from, to) {
    const startsAt = `${formatDate(from)}`;
    let endsAt = formatDate(to);
    endsAt = endsAt ? ` - ${endsAt}` : '';
    return `${startsAt}${endsAt}`;
}




/**
 * setup priorities
 */
 let priorities = ref([]);

function iconableTemplate(icon, color, text) {
    const container = document.createElement("div");
    createApp(Iconable, { icon: icon, color: color, text: text }).mount(container);
    const iconable = container.innerHTML;
    container.remove();
    return iconable;
}

function initPriority(input, plugins, onInitialize, onChange) {
    let priorityInputSettings = {
        plugins: plugins,
        options: priorities.value,
        valueField: 'id',
        render:{
            option: function(data, escape) {
                return iconableTemplate('flag-fill', escape(data.color), escape(data.name));
            },
            item: function(data, escape) {
                return iconableTemplate('flag-fill', escape(data.color), escape(data.name));
            },
        },
        onInitialize: onInitialize,
        onChange: onChange,
    };

    new TomSelect(input, priorityInputSettings);
}




/**
 * 
 */
 const dtColumns = [
    { data: "title" },
    { data: "created_at", title: 'Create Date', orderable: true },
    { data: "due_at", title: 'Due Date', orderable: true },
    { data: "priority_id", title: 'Priority', orderable: true },
];




/**
 * setup filters and sorters
 */
const defaultFilter = {
    title: null,
    priority_id: null,
    due_at: null,
};

let filter = ref(Object.assign({}, defaultFilter));

const defaultSort = {
    column: dtColumns.findIndex(column => column.orderable === true),
    dir: 'desc',
};

let sort = ref(Object.assign({}, defaultSort));

let priorityFilter;

function resetFilter() {
    filter.value = Object.assign({}, defaultFilter);
    priorityFilter.clear(true);
}

function resetSort() {
    sort.value = Object.assign({}, defaultSort);
}

// setup priority filter
function priorityFilterInitialized() {
    // set look n feel
    this.control.classList.add('shadow-none');
    this.control_input.classList.add('d-none');

    priorityFilter = this;
}

function applyFilter() {
    paginator.value.dt.ajax.reload();
}




/**
 * setup task editor in modal
 */
let taskModal;

function emptyTask() {
    return {
        id: null,
        title: null,
        description: null,
        due_at: null,
        completed_at: null,
        archived_at: null,
        deleted_at: null,
        priority: null,
        priority_id: null,
        media_count: null,
        tags_count: null,
        media: [],
        tags: [],
        newMedia: [],
        newTags: [],
        removedMedia: [],
        removedTags: [],
    }
};

let task = ref(emptyTask());

async function editTask(t) {
    task.value = emptyTask();

    if (t) {
        const details = (await api.task.show(t.id)).data;
        task.value = Object.assign(task.value, details);
    }

    // sync description model bindings
    setDescriptionInput(task.value.description);

    // setup datepicker
    let due_at = task.value.due_at;
    if (due_at) {
        due_at = DateTime.fromISO(due_at).toJSDate();
    }

    // set default priority as pre selected if needed
    let priority = priorities.value.filter((priority) => priority.id == task.value.priority_id)[0];

    if (!priority) {
        priority = priorities.value.filter((priority) => priority.default)[0];
    }

    task.value.priority = priority;
    priorityInput.setValue(priority.id);
    
    // clear tags input then set new tags if has any
    tagsInput.off('item_remove');
    tagsInput.clear(true);
    tagsInput.clearOptions();

    let tags = task.value.tags;

    if (tags && tags.length > 0) {
        tagsInput.addOptions(tags);
        tags.forEach((tag) => tagsInput.addItem(tag.id, true));
    }

    tagsInput.on('item_remove', tagRemoved);

    taskModal.show();
};

function initModal() {
    taskModal = new Modal('#task-modal');
}

function filterProps(obj, shouldRemoves = []) {
    let cloned = Object.assign({}, obj);

    for (const key in cloned) {
        shouldRemoves.forEach(shouldRemove => {
            if (shouldRemove(key, obj[key])) {
                delete cloned[key];
            }
        });
    }

    return cloned;
}

function makeTaskPayload(savedTask = {}, keys = []) {
    function removeEmpty(key, value) { return !value };
    function removeArray(key, value) { return value && value.constructor.name === 'Array' };
    function removeKeys(key, value) { return !keys.includes(key) };

    const sanitized = filterProps(task.value, [removeEmpty, removeArray]);

    return filterProps(Object.assign(savedTask, sanitized), [removeKeys]);
}

function uploadNewMedia(taskId) {
    async function process(media, resolve, reject) {
        const uploaded = (await api.media.upload(media, taskId)).data;
        const element = document.querySelector(`[data-id="${media.id}"]`);
        
        const linkAttribute = {
            HTMLAnchorElement: 'href',
            HTMLImageElement: 'src',
        };

        element.setAttribute('data-id', uploaded.id);
        element.setAttribute('data-uploaded', '');
        element.setAttribute(linkAttribute[element.constructor.name], uploaded.link);

        // sync model binding
        task.value.description = descriptionInput.html.get();

        resolve();
    }

    let cloned = Object.assign([], task.value.newMedia);
    const processes = [];

    cloned.forEach((media, index) => {
        processes[index] = new Promise((resolve, reject) => process(media, resolve, reject));
        
    });

    return processes;
}

function deleteRemovedMedia() {
    async function process(media, resolve, reject) {
        await api.media.delete(media.id);

        resolve();
    }

    let cloned = Object.assign([], task.value.removedMedia);
    const processes = [];

    cloned.forEach((media, index) => {
        processes[index] = new Promise((resolve, reject) => process(media, resolve, reject));
        
    });

    return processes;
}

function createNewTags(taskId) {
    async function process(tag, resolve, reject) {
        await api.tag.create(tag.name, taskId);
        resolve();
    }

    let cloned = Object.assign([], task.value.newTags);
    const processes = [];

    cloned.forEach(async (tag, index) => {
        processes[index] = new Promise((resolve, reject) => process(tag, resolve, reject));
    });

    return processes;
}

function deleteRemovedTags() {
    async function process(tag, resolve, reject) {
        await api.tag.delete(tag.id);
        resolve();
    }

    let cloned = Object.assign([], task.value.removedTags);
    const processes = [];

    cloned.forEach(async (tag, index) => {
        processes[index] = new Promise((resolve, reject) => process(tag, resolve, reject));
    });

    return processes;
}

async function saveTask(target) {
    const keys = ['id', 'title', 'description', 'due_at', 'priority_id', 'completed_at', 'archived_at'];

    let payload = makeTaskPayload(target, keys);

    let savedTask = (await api.task.update(payload)).data;
    savedTask.priority = priorities.value.filter((priority) => priority.id == savedTask.priority_id)[0];

    const listed = tasks.value.findIndex(task => task.id === savedTask.id);

    if (listed >= 0) {
        tasks.value[listed] = savedTask;
    }
    else {
        resetFilter();
        resetSort();

        paginator.value.dt.ajax.reload();
    }

    taskModal.hide();
}

async function saveEditor() {
    if (!taskForm.reportValidity()) {
        return;
    }

    let target = Object.assign({}, task.value);

    if (!target.id) {
        target = (await api.task.create()).data;
    }

    Promise
        .all(
            uploadNewMedia(target.id)
            .concat(createNewTags(target.id))
            .concat(deleteRemovedMedia())
            .concat(deleteRemovedTags())
        )
        .then(() => saveTask(target));
}

// setup description input
let descriptionInput;

function setDescriptionInput(value) {
    const input = document.querySelector('#description-input');
    input.value = value;
    input.dispatchEvent(new Event('change'));

    // sync model bindings
    document.querySelector('#description-validator').value = value;
    task.value.description = value;
    descriptionInput.html.set(value);
}

function initFroala() {
    FroalaEditor.DefineIcon('file', { NAME: 'file', SVG_KEY: 'insertFile'});

    FroalaEditor.RegisterQuickInsertButton('file', {
        icon: 'file',
        title: 'Any file',
        callback: function () {
            var e = this,
                t = e.$;
                
            e.shared.$qi_file_input || 
            (
                e.shared.$qi_file_input = t(document.createElement("input"))
                    .attr("accept", "application/*, audio/*, font/*, model/*, text/*, video/*")
                    .attr("name", "quickInsertFile".concat(this.id))
                    .attr("style", "display: none;")
                    .attr("type", "file"),

                t("body").first().append(e.shared.$qi_file_input),
                e.events.$on(e.shared.$qi_file_input, "change", function() {
                    var e = t(this).data("inst");
                    this.files && (
                        e.quickInsert.hide(),
                        e.file.upload(this.files)
                    ),
                    t(this).val("")
                }, !0)
            ),

            e.$qi_file_input = e.shared.$qi_file_input,
            e.helpers.isMobile() && e.selection.save(),
            e.events.disableBlur(),
            e.$qi_file_input.data("inst", e)[0].click()
        },
        undo: false
    });

    function copyMedia(file) {
        const id = task.value.newMedia.length + 1;
        file.id = `media-${id}`;
        task.value.newMedia.push(file);
    }

    function mediaInserted(element) {
        const id = task.value.newMedia.length;
        element.setAttribute('data-id', `media-${id}`);
    }

    async function mediaRemoved(element) {
        const uploaded = element.hasAttribute('data-uploaded');
        const id = element.getAttribute('data-id');
        let removed;

        if (uploaded) {
            task.value.removedMedia.push({id: id});
            removed = task.value.removedMedia.map(media => media.id === id);
        }
        else {
            task.value.newMedia = task.value.newMedia.filter(media => media.id !== id);
            removed = !task.value.newMedia.map(media => media.id === id);
        }

        return removed;
    }

    const froalaSettings = {
        placeholderText: 'Write task descriptions along with attachments here..',
        charCounterCount: false,
        toolbarInline: true,
        quickInsertButtons: ['file', 'image', 'embedly', 'table', 'ul', 'ol', 'hr'],
        events: {
            initialized: function() {
                this.$box[0].classList.add('form-control', 'border-0', 'shadow-none');
                this.el.classList.add('text-break', 'vh-60', 'overflow-y-overlay');
                descriptionInput = this;
            },
            blur: function() {
                setDescriptionInput(this.html.get());
            },
            'file.beforeUpload': function (files) {
                copyMedia(files[0]);
            },
            'file.inserted': function ($file, response) {
                mediaInserted($file[0], response);
            },
            'file.unlink': function (link) {
                return mediaRemoved(link);
            },
            'image.beforeUpload': function (images) {
                copyMedia(images[0]);
            },
            'image.inserted': function ($img, response) {
                mediaInserted($img[0], response);
            },
            'image.beforeRemove': function ($img) {
                return mediaRemoved($img[0]);
            },
        }
    };
    
    new FroalaEditor('#description-input', froalaSettings);
}

// setup priority options
let priorityInput;

function priorityInputInitialized() {
    // set look n feel
    this.control.classList.add('bg-transparent', 'border-0', 'shadow-none');
    this.control_input.classList.add('d-none');

    priorityInput = this;
}

function priorityInputChanged(value) {
    task.value.priority_id = value;
}

// setup tags input
let tagsInput;

async function tagRemoved(value, $item) {
    const newTagIndex = task.value.newTags.findIndex((tag) => tag.id === value);

    if (newTagIndex >= 0) {
        delete task.value.newTags[newTagIndex];
    }
    else {
        task.value.removedTags.push({id: value});
    }

    tagsInput.removeOption(value);
}

function initTagsInput() {
    let tagsInputSettings = {
        valueField: 'id',
        labelField: 'name',
        searchField: [],
        persist: false,
        plugins: ['remove_button'],
        create: async function(input, callback) {
            const id = task.value.newTags.length + 1;
            const tag = {id: id, name: input};

            // sync to task
            task.value.newTags.push(tag);

            callback(tag);
        },
        onInitialize: function() {
            // set look n feel
            this.control.classList.add('bg-transparent', 'border-0', 'shadow-none');

            tagsInput = this;
        },
    };

    new TomSelect('#tags-input', tagsInputSettings);
}



/**
 * setup datatables for pagination control
 */
DataTable.use(DataTablesCore);

const paginator = ref();

let tasks = ref([]);

function dtSettings (completed = false, archived = false) {
    return {
        searching: false,
        autoWidth: false,
        deferRender: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: `/api/task/list?completed=${completed}&archived=${archived}`,
            // type: "post",
            data: (data) => {
                data.columns[0].search.value = filter.value.title;
                data.columns[3].search.value = filter.value.priority_id;

                if (filter.value.due_at) {
                    data.columns[2].search.value = formatDateRange(filter.value.due_at[0], filter.value.due_at[1]);
                    data.columns[2].search.from = toLuxon(filter.value.due_at[0]).toISO();
                    data.columns[2].search.to = toLuxon(filter.value.due_at[1]).toISO();
                }

                data.order[0]['column'] = sort.value.column;
                data.order[0]['dir'] = sort.value.dir;
            },
            beforeSend: (xhr) => {
                const authStore = useAuthStore();

                if (authStore.loggedIn) {
                    xhr.setRequestHeader("Authorization", `${authStore.token.type} ${authStore.token.value}`);
                }
            },
        },
        columns: dtColumns,
        language: {
            lengthMenu: "_MENU_",
            paginate: {
                next: '<i class="bi bi-caret-right-fill"></i>',
                previous: '<i class="bi bi-caret-left-fill"></i>',
            },
        },
        dom: `<'d-flex justify-content-end'f><'#content'><'#pagination.row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'l><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>><'d-none'rit>`,
        preDrawCallback: (settings) => {
            tasks.value = settings.aoData.map((aoData) => {
                let row = aoData._aData;
                row.priority = priorities.value.filter((priority) => priority.id == row.priority_id)[0];
                return row;
            });
            
            const wrapper = document.getElementById(`${settings.sTableId}_wrapper`);
            const api = settings.oInstance.DataTable();

            wrapper
                .querySelector('#pagination')
                .classList
                .toggle('d-none', api.page.info().pages <= 1);
        },
    };
};





/**
 * generic task status modifiers
 */
async function updateStatus(subject, task, status) {
    let target = Object.assign({}, task);

    if (!target.id) {
        target[`${subject}_at`] = status;
        return;
    }
    
    try {
        await api.task[subject](target.id, status);

        paginator.value.dt.ajax.reload(null, tasks.value.length === 1);

        if (taskModal._isShown) {
            taskModal.hide();
        }
    } catch (error) {
        // popup the error
    }
}

onMounted(function () {
    api.priority.list().then((response) => {
        priorities.value = response.data;
        initPriority('#filter-priority', ['dropdown_input', 'clear_button'], priorityFilterInitialized);
        initPriority('#priority-input', ['dropdown_input'], priorityInputInitialized, priorityInputChanged);
    });
    
    initModal();
    initFroala();
    initTagsInput();
});
</script>

<style>
@import '@vuepic/vue-datepicker/dist/main.css';
@import "froala-editor/css/froala_editor.pkgd.min.css";
@import 'tom-select/dist/css/tom-select.bootstrap5.css';
@import '&/task.css';
</style>