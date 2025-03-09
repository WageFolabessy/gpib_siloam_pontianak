document.addEventListener("DOMContentLoaded", function (event) {

    const showNavbar = (toggleId, navId, bodyId, headerId) => {
        const toggle = document.getElementById(toggleId),
            nav = document.getElementById(navId),
            bodypd = document.getElementById(bodyId),
            headerpd = document.getElementById(headerId)

        // Validate that all variables exist
        if (toggle && nav && bodypd && headerpd) {
            toggle.addEventListener('click', () => {
                // show navbar
                nav.classList.toggle('show')
                // change icon
                toggle.classList.toggle('bx-x')
                // add padding to body
                bodypd.classList.toggle('body-pd')
                // add padding to header
                headerpd.classList.toggle('body-pd')
            })
        }
    }

    showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header')

    /*===== LINK ACTIVE =====*/
    const linkColor = document.querySelectorAll('.nav_link')

    function colorLink() {
        if (linkColor) {
            linkColor.forEach(l => l.classList.remove('active'))
            this.classList.add('active')
        }
    }
    linkColor.forEach(l => l.addEventListener('click', colorLink))

    // Your code to run since DOM is loaded and ready
});


document.getElementById('renunganDropdown').addEventListener('click', function () {
    this.classList.toggle('dropdown_open');
});

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    let renunganDropdown = document.getElementById('renunganDropdown');
    if (!renunganDropdown.contains(event.target)) {
        renunganDropdown.classList.remove('dropdown_open');
    }
});

document.getElementById('renunganDropdown').addEventListener('click', function () {
    let dropdownContent = this.querySelector('.dropdown_content');
    let navList = document.getElementById('navList');
    
    if (dropdownContent.style.display === 'block') {
        dropdownContent.style.display = 'none';
        navList.style.marginBottom = '0';
    } else {
        dropdownContent.style.display = 'block';
        // Calculate the height of the dropdown content including margins
        let dropdownHeight = dropdownContent.offsetHeight;
        // Adjust margin-bottom to accommodate the dropdown content
        navList.style.marginBottom = dropdownHeight + 'px';
    }
});

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    let renunganDropdown = document.getElementById('renunganDropdown');
    let dropdownContent = renunganDropdown.querySelector('.dropdown_content');
    let navList = document.getElementById('navList');
    
    if (!renunganDropdown.contains(event.target)) {
        dropdownContent.style.display = 'none';
        navList.style.marginBottom = '0';
    }
});


