Echo.channel('laravel_database_report')
.listen('Report', (data) => {
    pagination();
    console.log(data)
})
