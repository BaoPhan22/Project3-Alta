const selectedDateInput = document.getElementById("date_order");

document.getElementById("date_order").addEventListener("change", function () {
    const selectedDate = new Date(selectedDateInput.value);
    const curDate = new Date();
    // console.log(selectedDate);
    // console.log(curDate);
    if (curDate.getDay() <= selectedDate.getDay()) console.log("Đặt đi bà");
    else console.log("Không đặt được");
});
