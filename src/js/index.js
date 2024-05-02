import $ from 'jquery';
import moment from "moment/moment";
import DataTable from 'datatables.net-dt';
import Attend from "./attend";
import AttendApi from "./attend-api";

'use strict';


let SummaryWidget = (function (selector) {


    let classrooms = [];
    let students   = [];
    let schedules  = [];


    let table = new DataTable(selector + " table", {
        data: classrooms,
        searching: false,
        info: false,
        paging: false,
        ordering: false,
        columns: [{
            data: null,
            render: (data) => {
                return `${data.Label} (${data['Students'].length})`;
            }
        }, {
            data: "Summary",
            render: (data) => data[0]
        }, {
            data: "Summary",
            render: (data) => data[1]
        }, {
            data: "Summary",
            render: (data) => data[2]
        }, {
            data: "Summary",
            render: (data) => data[3]
        }, {
            data: "Summary",
            render: (data) => data[4]
        }]
    });


    async function load() {
        Attend.loadAnother();
        try {
            return Promise.all([AttendApi.classrooms.select(), AttendApi.students.select(), AttendApi.schedules.select()])
                .then((values) => {
                    classrooms = values[0].sort((a, b) => {
                        if ( a.Ordering < b.Ordering ) return -1;
                        if ( a.Ordering > b.Ordering ) return 1;
                        return 0;
                    });
                    students   = values[1];
                    schedules  = values[2];


                    classrooms.forEach((classroom, idx, arr) => {

                        // Sort the students into their classrooms
                        classroom['Students'] = students.filter((student) => student.Enrolled && (classroom.Id === student.ClassroomId));


                        // Identify, sort and assign each student's schedules
                        classroom['Students'].forEach((student, idx1, arr1) => {
                            student['Schedules'] = schedules
                                .filter((schedule) => schedule.StudentId === student.Id)
                                .sort((a, b) => {
                                    if (moment(a.StartDate) < moment(b.StartDate)) {
                                        return 1;
                                    } else if (moment(a.StartDate) > moment(b.StartDate)) {
                                        return -1;
                                    }
                                    return 0;
                                });
                        });

                        // Summarize schedules
                        classroom['Summary'] = [0, 0, 0, 0, 0];   // # of students attending each day of the week
                        classroom['Students'].forEach((student) => {
                            let sched   = student['Schedules'][0]['Schedule'];
                            let summary = classroom['Summary'];

                            // Check which (if any) days of the week the student is attending
                            for (let i = 0; i < 5; i++) {
                                let bitValue = 1 << i;                    // Calculate the bit value for the current index (2^i)
                                if (sched & bitValue) {                   // Check if the AM bit is set for this student for this day
                                    summary[i]++;
                                } else if (sched & (bitValue * 0x20)) {   // Check if the lunch bit is set
                                    summary[i]++;
                                } else if (sched & (bitValue * 0x400)) {   // Check the PM bit
                                    summary[i]++;
                                }
                            }
                        });
                    });

                    Attend.doneLoading();
                })
                .catch((err) => {
                    console.error(err);
                    alert("Students didn't load");
                    Attend.doneLoading();
                });
        } catch (e) {
            console.log(e);
            Attend.doneLoading();
        }
    }

    function populate() {
        table.clear();
        for (let i = 0; i < classrooms.length; i++) {
            table.row.add(classrooms[i]);
        }
        table.draw();
        return this;
    }

    return {load, populate};
})('#summary-widget');


$(async function () {
    console.log("Index page ready");
    await SummaryWidget.load();
    SummaryWidget.populate();
});
