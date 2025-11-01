<!-- Main Builder Parts Container -->
<div style="display: flex; height: 100%; width: 100%; overflow: hidden; padding-bottom: 12px;" x-data="{
    leftCollapsed: localStorage.getItem('wkb-leftCollapsed') === 'true' || false,
    rightCollapsed: localStorage.getItem('wkb-rightCollapsed') === 'true' || false,
    toggleLeft() {
        this.leftCollapsed = !this.leftCollapsed;
        localStorage.setItem('wkb-leftCollapsed', this.leftCollapsed);
    },
    toggleRight() {
        this.rightCollapsed = !this.rightCollapsed;
        localStorage.setItem('wkb-rightCollapsed', this.rightCollapsed);
    }
}" x-init="setTimeout(() => { $el.querySelector('.wkb-sidebar-blocks').style.width = leftCollapsed ? '64px' : '300px'; $el.querySelector('.wkb-sidebar-blocks').style.minWidth = leftCollapsed ? '64px' : '300px'; $el.querySelector('.wkb-properties').style.width = rightCollapsed ? '60px' : '300px'; $el.querySelector('.wkb-properties').style.minWidth = rightCollapsed ? '60px' : '300px'; }, 10)"
    :style="{
        '--left-width': leftCollapsed ? '64px' : '300px',
        '--right-width': rightCollapsed ? '60px' : '300px'
    }">
    <!-- Left Sidebar -->
    <div :style="leftCollapsed ? 'width: 64px; min-width: 64px;' : 'width: 300px; min-width: 300px;'"
        :class="leftCollapsed ? 'wkb-panel wkb-sidebar-blocks collapsed force-margins' :
            'wkb-panel wkb-sidebar-blocks force-margins'"
        style="margin-left: var(--wkb-sidebar-margin) !important; margin-right: var(--wkb-sidebar-margin) !important;">
        @includeIf('website-builder::builder.V1.components.sidebar-blocks')
    </div>

    <!-- Center Canvas -->
    <div class="wkb-panel" style="flex: 1; overflow: hidden; position: relative; min-width: 0;">
        @includeIf('website-builder::builder.V1.components.canvas')
    </div>

    <!-- Right Sidebar -->
    <div :style="rightCollapsed ? 'width: 60px; min-width: 60px;' : 'width: 300px; min-width: 300px;'"
        :class="rightCollapsed ? 'wkb-panel wkb-properties collapsed force-margins' :
            'wkb-panel wkb-properties force-margins'"
        style="margin-left: var(--wkb-sidebar-margin) !important; margin-right: var(--wkb-sidebar-margin) !important;">
        @includeIf('website-builder::builder.V1.components.properties')
    </div>
</div>
