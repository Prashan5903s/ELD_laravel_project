"use strict";
var KTUsersPermissionsList = (function () {
    var t, e;
    return {
        init: function () {
            (e = document.querySelector("#kt_tr_u_table")) &&
                (e.querySelectorAll("tbody tr").forEach((t) => {
                    const e = t.querySelectorAll("td"),
                        // Ensure the format is consistent with how DataTables expects it for sorting
                        n = moment(e[4].innerHTML, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");
                    e[4].setAttribute("data-order", n);
                }),
                (t = $(e).DataTable({
                    paging: true, // Enable pagination
                    pageLength: 10, // Set number of rows per page
                    pagingType: "full_numbers", // Set pagination type
                    order: [[4, 'desc']], // Set default sorting by 'Shift change time' column in descending order
                    // Other configuration options...
                })),
                document
                    .querySelector(
                        '[data-kt_tr_u_table-filter="search"]'
                    )
                    .addEventListener("keyup", function (e) {
                        t.search(e.target.value).draw();
                    }),
                e
                    .querySelectorAll(
                        '[data-kt_tr_u_table-filter="delete_row"]'
                    )
                    .forEach((e) => {
                        e.addEventListener("click", function (e) {
                            e.preventDefault();
                            const n = e.target.closest("tr"),
                                o = n.querySelectorAll("td")[0].innerText;
                            Swal.fire({
                                text:
                                    "Are you sure you want to delete " +
                                    o +
                                    "?",
                                icon: "warning",
                                showCancelButton: !0,
                                buttonsStyling: !1,
                                confirmButtonText: "Yes, delete!",
                                cancelButtonText: "No, cancel",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-danger",
                                    cancelButton:
                                        "btn fw-bold btn-active-light-primary",
                                },
                            }).then(function (e) {
                                e.value
                                    ? Swal.fire({
                                          text: "You have deleted " + o + "!.",
                                          icon: "success",
                                          buttonsStyling: !1,
                                          confirmButtonText: "Ok, got it!",
                                          customClass: {
                                              confirmButton:
                                                  "btn fw-bold btn-primary",
                                          },
                                      }).then(function () {
                                          t.row($(n)).remove().draw();
                                      })
                                    : "cancel" === e.dismiss &&
                                      Swal.fire({
                                          text:
                                              o +
                                              " was not deleted.",
                                          icon: "error",
                                          buttonsStyling: !1,
                                          confirmButtonText: "Ok, got it!",
                                          customClass: {
                                              confirmButton:
                                                  "btn fw-bold btn-primary",
                                          },
                                      });
                            });
                        });
                    }));
        },
    };
})();
KTUtil.onDOMContentLoaded(function () {
    KTUsersPermissionsList.init();
});
