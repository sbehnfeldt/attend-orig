'use strict';

let AttendApi = (function () {
    let template = function (k, v) {
        return {
            // Select all records
            select: async function () {
                const response = await fetch(`/api/${k}`);
                if (!response.ok) {
                    throw new Error(`HTTP error status: ${response.status}`)
                }
                let json = await response.json();
                return json.data;   // All records
            },

            // Create and insert a new record
            insert: async function (data) {
                const response = await fetch(`/api/${k}`, {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(data)
                });
                if (!response.ok) {
                    throw new Error(`HTTP error status: ${response.status}`)
                    return [];
                }
                let json = await response.json();
                return json.data;   // The new record
            },

            // Update an existing record
            update: async function (data) {
                const response = await fetch(`/api/${k}/${data.Id}`, {
                    method: 'PUT',
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(data)
                });
                if (!response.ok) {
                    throw new Error(`HTTP error status: ${response.status}`)
                    return [];
                }
                let json = await response.json();
                return json.data;   // The updated record
            },

            // Delete an existing record
            remove: async function (id) {
                const response = await fetch(`/api/${k}/${id}`, {
                    method: 'DELETE'
                });
                if (!response.ok) {
                    throw new Error(`HTTP error status: ${response.status}`)
                }
                return;
            }
        }
    }

    return {
        classrooms: template('classrooms'),
        students: template('students'),
        schedules: template('schedules')
    };
})();

export default AttendApi;
