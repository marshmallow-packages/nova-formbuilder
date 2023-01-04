<script>
    document.addEventListener('alpine:init', () => {
        // Magic: $tooltip
        Alpine.magic('tooltip', el => message => {
            let instance = tippy(el, {
                content: message,
                trigger: 'manual'
            })

            instance.show()

            setTimeout(() => {
                instance.hide()

                setTimeout(() => instance.destroy(), 150)
            }, 2000)
        })

        // Directive: x-tooltip
        Alpine.directive('tooltip', (el, {
            expression
        }) => {
            tippy(el, {
                allowHTML: true,
                content: expression
            })
        })
    })
</script>
