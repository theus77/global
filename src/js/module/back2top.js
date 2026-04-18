export default function back2top (selector, offset) {
    const backToTop = document.querySelector(selector)
    const scrollOffset = offset || 60

    if (backToTop === null) return;

    backToTop.addEventListener('click', function(e) {
        e.preventDefault()
        const targetId = this.getAttribute('data-target')
        const target = document.getElementById(targetId) || document.body
        target.setAttribute('tabindex', '-1')
        target.focus()
        target.scrollIntoView({ behavior: "smooth" })

        function removeTabIndex(){
            target.removeAttribute('tabindex')
            target.removeEventListener('focus', removeTabIndex)
        }

        target.addEventListener('focus', removeTabIndex)
    })

    function scrollCallback() {
        backToTop.style.transition = 'opacity 0.3s ease-in-out'
        if (window.scrollY > scrollOffset) {
            backToTop.style.opacity = '1'
        } else {
            backToTop.style.opacity = '0'
        }
    }

    scrollCallback();
    window.addEventListener('scroll', scrollCallback)
}
