function toggleFields(){
    var userType = document.getElementById("usertype").value;
    var isStudentFields = document.getElementById("student-container");
    var isStaffFields = document.getElementById("staff-container");

    isStudentFields.style.display = none;
    isStaffFields.style.display = none;

    if(userType === "student"){
        isStudentFields.style.display = block;
    } else if(userType === "staff"){
        isStaffFields.style.display = block;
    }

}