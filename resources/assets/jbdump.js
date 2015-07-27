function jbdump() {
}

jbdump.reclass = function (el, className) {
    if (el.className.indexOf(className) < 0) {
        el.className += " " + className;
    }
};

jbdump.unclass = function (el, className) {
    if (el.className.indexOf(className) > -1) {
        el.className = el.className.replace(" " + className, "");
    }
};

jbdump.toggle = function (el) {
    var ul = el.parentNode.getElementsByTagName("ul");
    for (var i = 0; i < ul.length; i++) {
        if (ul[i].parentNode.parentNode == el.parentNode) {
            ul[i].parentNode.style.display = ul[i].parentNode.style.display == "none" ? "block" : "none";
        }
    }
    if (ul[0].parentNode.style.display == "block") {
        jbdump.reclass(el, "jbopened");
    } else {
        jbdump.unclass(el, "jbopened");
    }
};
