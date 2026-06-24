"use strict";
var KTUsersPermissionsList = (function () {
    var t, e;
    return {
        init: function () {
            (e = document.querySelector("#kt_lang_table")) &&
                (e.querySelectorAll("tbody tr").forEach((row) => {
                    const cells = row.querySelectorAll("td"),
                        dateCell = cells[2];
                    if (dateCell) {
                        const formattedDate = moment(dateCell.textContent.trim(), "DD MMM YYYY, LT").format();
                        dateCell.setAttribute("data-order", formattedDate);
                    }
                }),
                (t = $(e).DataTable({
                    paging: true, // Enable pagination
                    pageLength: 10, // Set number of rows per page
                    pagingType: "full_numbers", // Set pagination type
                    // Other configuration options...
                })),
                document
                    .querySelector('[data-kt-lang-table-filter="search"]')
                    .addEventListener("keyup", function (event) {
                        t.search(event.target.value).draw();
                    }));
        },
    };
})();
KTUtil.onDOMContentLoaded(function () {
    KTUsersPermissionsList.init();
});
