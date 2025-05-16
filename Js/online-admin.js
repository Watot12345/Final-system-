const employeeData = {
  giray: {
    name: "John Mark Giray",
    profileImage: "image/GirayProf.jpg",
    timeIn: "09:00",
    timeOut: "17:00",
    lateTime: "0 mins",
    overtime: "30 mins",
    deductions: "₱5.00",
  },
  lacsi: {
    name: "Rhyzon Lacsi",
    profileImage: "image/LacsiPRof.jpg",
    timeIn: "09:30",
    timeOut: "17:30",
    lateTime: "30 mins",
    overtime: "0 mins",
    deductions: "₱0",
  },
  trono: {
    name: "Jaime Trono",
    profileImage: "image/TronoProf.jpg",
    timeIn: "10:00",
    timeOut: "18:00",
    lateTime: "60 mins",
    overtime: "0 mins",
    deductions: "₱10.00",
  },
};

// Function to toggle the visibility of the attendance info for each employee
function toggleAttendanceInfo(employeeId) {
  const attendanceInfo = document.getElementById(
    employeeId + "-attendance-info"
  );
  const button = document.querySelector("#" + employeeId + "-container button");

  if (attendanceInfo.style.display === "block") {
    attendanceInfo.style.display = "none";
    button.innerText = "Informations";
  } else {
    attendanceInfo.style.display = "block";
    button.innerText = "Close";
  }
}

// Function to update the attendance info for each employee
function updateAttendanceInfo() {
  for (const employeeId in employeeData) {
    const employee = employeeData[employeeId];

    // Update profile image and name
    document.getElementById(employeeId + "-profile-img").src =
      employee.profileImage;
    document.getElementById(employeeId + "-name").textContent = employee.name;

    // Update attendance details
    document.getElementById(employeeId + "-employee-name").textContent =
      employee.name;
    document.getElementById(employeeId + "-time-in").textContent =
      employee.timeIn;
    document.getElementById(employeeId + "-time-out").textContent =
      employee.timeOut;
    document.getElementById(employeeId + "-late-time").textContent =
      employee.lateTime;
    document.getElementById(employeeId + "-overtime").textContent =
      employee.overtime;
    document.getElementById(employeeId + "-deductions").textContent =
      employee.deductions;
  }
}

// Initial call to populate data for all employees
updateAttendanceInfo();
