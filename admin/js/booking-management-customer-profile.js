document.addEventListener("DOMContentLoaded", function () {
	document.getElementById("cust_personal_data").style.display = "block";
	jQuery(function ($) { $('.customer_tables').DataTable(); });
});

function openCustomerProfileTab(evt, sectionName) {

	const tabContents = document.getElementsByClassName("tabcontent");
	for (let i = 0; i < tabContents.length; i++) {
		tabContents[i].style.display = "none";
		tabContents[i].classList.remove("active");
	}

	const tabLinks = document.getElementsByClassName("tablink");
	for (let i = 0; i < tabLinks.length; i++) {
		tabLinks[i].classList.remove("tab-active");
	}

	document.getElementById(sectionName).style.display = "block";
	document.getElementById(sectionName).classList.add("active");

	evt.currentTarget.classList.add("tab-active");
}


