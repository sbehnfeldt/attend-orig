const path=require( 'path' );

module.exports = {
    mode: 'development',
    entry: {
        'classrooms' : './src/js/classrooms.js',
        'enrollment' : './src/js/enrollment.js',
        'attendance' : './src/js/attendance.js',
    },

    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'public/js' ),
    },
}
