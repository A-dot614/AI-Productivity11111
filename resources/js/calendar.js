import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

const initCalendar = () => {
    const el = document.getElementById('calendar');

    if (!el) {
        return;
    }

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        editable: true,
        selectable: true,
        dayMaxEvents: true,
        nowIndicator: true,
        height: 'auto',
        events: (info, successCallback, failureCallback) => {
            fetch(el.dataset.eventsUrl + `?start=${encodeURIComponent(info.startStr)}&end=${encodeURIComponent(info.endStr)}`, {
                headers: { Accept: 'application/json' },
            })
                .then((res) => res.json())
                .then(successCallback)
                .catch(failureCallback);
        },
        eventDrop: (info) => {
            handleMove(info.event, info.delta);
        },
        eventResize: (info) => {
            handleMove(info.event, info.delta);
        },
        select: (info) => {
            openEventModal(info.startStr, info.endStr || null, info.allDay);
        },
        eventClick: (info) => {
            const props = info.event.extendedProps;

            if (props.type === 'task') {
                window.location.href = el.dataset.taskUrl.replace('__ID__', props.task_id);
                return;
            }

            openEditEventModal(info.event, props);
        },
    });

    const handleMove = (fcEvent, delta) => {
        const props = fcEvent.extendedProps;
        const start = fcEvent.start.toISOString();

        if (props.type === 'task') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = el.dataset.rescheduleUrl.replace('__ID__', props.task_id);

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PATCH';
            form.appendChild(method);

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = el.dataset.token;
            form.appendChild(csrf);

            const due = document.createElement('input');
            due.type = 'hidden';
            due.name = 'due_date';
            due.value = start;
            form.appendChild(due);

            document.body.appendChild(form);
            form.submit();

            return;
        }

        const data = new FormData();
        data.append('_method', 'PATCH');
        data.append('_token', el.dataset.token);
        data.append('start_at', start);
        if (fcEvent.end) {
            data.append('end_at', fcEvent.end.toISOString());
        }

        fetch(el.dataset.moveUrl.replace('__ID__', props.event_id), {
            method: 'POST',
            body: data,
            headers: { Accept: 'application/json' },
        })
            .then((res) => res.json())
            .catch(() => window.toast.error('Could not move the event', 'Something went wrong while saving.'));
    };

    const openEventModal = (start, end, allDay) => {
        const modal = document.getElementById('event-modal');

        if (!modal) {
            return;
        }

        document.getElementById('event_title').value = '';
        document.getElementById('event_description').value = '';
        document.getElementById('event_start_at').value = start.slice(0, 16);
        document.getElementById('event_end_at').value = end ? end.slice(0, 16) : '';
        document.getElementById('event_all_day').checked = allDay;
        document.getElementById('event_color').value = '#6366f1';
        modal.dispatchEvent(new CustomEvent('open-create-event-modal'));
    };

    const openEditEventModal = (fcEvent, props) => {
        const editModal = document.getElementById('event-edit-modal');
        const data = {
            id: props.event_id,
            title: fcEvent.title,
            start_at: fcEvent.start ? fcEvent.start.toISOString().slice(0, 16) : '',
            end_at: fcEvent.end ? fcEvent.end.toISOString().slice(0, 16) : '',
            all_day: fcEvent.allDay,
            description: props.description || '',
            color: fcEvent.backgroundColor || '#6366f1',
            delete_url: el.dataset.eventDeleteUrl.replace('__ID__', props.event_id),
        };

        editModal.dispatchEvent(new CustomEvent('edit-event', { detail: data }));
        editModal.dispatchEvent(new CustomEvent('open-edit-event-modal'));
    };

    calendar.render();
};

document.addEventListener('DOMContentLoaded', initCalendar);
