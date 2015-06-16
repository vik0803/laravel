function isElementInViewport(el) {
    var rect = el.getBoundingClientRect();
    // $(window).height() or (window.innerHeight || document.documentElement.clientHeight)
    // $(window).width() or (window.innerWidth || document.documentElement.clientWidth)
    return (rect.top >= 0 && rect.left >= 0 && rect.bottom <= $(window).height() && rect.right <= $(window).width());
}
