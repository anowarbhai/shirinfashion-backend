@extends('admin.layouts.master')

@section('title', 'Menu Manager - Shirin Fashion Admin')
@section('header', 'Menu Manager')

@section('content')
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm p-6">
    <p class="text-gray-600 mb-6">
        <i class="fas fa-info-circle mr-2 text-rose-600"></i>
        Create and manage menus for your website. Add links to pages, categories, or custom URLs.
    </p>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" id="menuTabs">
            <button type="button" class="menu-tab border-rose-500 text-rose-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="footer1">
                Footer Column 1
            </button>
            <button type="button" class="menu-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="footer2">
                Footer Column 2
            </button>
            <button type="button" class="menu-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="header">
                Header Menu
            </button>
            <button type="button" class="menu-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="mobile">
                Mobile Menu
            </button>
        </nav>
    </div>

    <!-- Menu Editor Container -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Panel: Menu Items -->
        <div class="lg:col-span-2">
            <div class="bg-gray-50 rounded-lg p-4 min-h-[400px]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" id="menuTitle">Footer Column 1</h3>
                    <button type="button" onclick="addMenuItem()" class="px-3 py-1.5 bg-rose-500 text-white text-sm rounded hover:bg-rose-600">
                        <i class="fas fa-plus mr-1"></i>Add Item
                    </button>
                </div>
                
                <div id="menuItems" class="space-y-2">
                    <!-- Menu items will be rendered here -->
                </div>
                
                <p id="emptyMenu" class="text-center text-gray-400 py-12">
                    <i class="fas fa-bars text-4xl mb-4 block"></i>
                    No items in this menu. Click "Add Item" to start building your menu.
                </p>
            </div>
        </div>

        <!-- Right Panel: Add Custom Link -->
        <div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-4">Add New Item</h3>
                
                <!-- Quick Links -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Links</label>
                    <div class="space-y-2">
                        <button type="button" onclick="addQuickLink('/')" class="w-full text-left px-3 py-2 bg-white border border-gray-200 rounded hover:border-rose-500 text-sm">
                            <i class="fas fa-home mr-2 text-gray-400"></i>Home
                        </button>
                        <button type="button" onclick="addQuickLink('/shop')" class="w-full text-left px-3 py-2 bg-white border border-gray-200 rounded hover:border-rose-500 text-sm">
                            <i class="fas fa-shopping-bag mr-2 text-gray-400"></i>Shop
                        </button>
                        <button type="button" onclick="addQuickLink('/categories')" class="w-full text-left px-3 py-2 bg-white border border-gray-200 rounded hover:border-rose-500 text-sm">
                            <i class="fas fa-th-large mr-2 text-gray-400"></i>Categories
                        </button>
                        <button type="button" onclick="addQuickLink('/about')" class="w-full text-left px-3 py-2 bg-white border border-gray-200 rounded hover:border-rose-500 text-sm">
                            <i class="fas fa-user mr-2 text-gray-400"></i>About Us
                        </button>
                        <button type="button" onclick="addQuickLink('/contact')" class="w-full text-left px-3 py-2 bg-white border border-gray-200 rounded hover:border-rose-500 text-sm">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>Contact
                        </button>
                        <button type="button" onclick="addQuickLink('/faq')" class="w-full text-left px-3 py-2 bg-white border border-gray-200 rounded hover:border-rose-500 text-sm">
                            <i class="fas fa-question-circle mr-2 text-gray-400"></i>FAQ
                        </button>
                        <button type="button" onclick="addQuickLink('/shipping')" class="w-full text-left px-3 py-2 bg-white border border-gray-200 rounded hover:border-rose-500 text-sm">
                            <i class="fas fa-shipping-fast mr-2 text-gray-400"></i>Shipping Info
                        </button>
                        <button type="button" onclick="addQuickLink('/returns')" class="w-full text-left px-3 py-2 bg-white border border-gray-200 rounded hover:border-rose-500 text-sm">
                            <i class="fas fa-undo mr-2 text-gray-400"></i>Returns
                        </button>
                    </div>
                </div>

                <!-- Custom Link Form -->
                <div class="border-t border-gray-200 pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Custom Link</label>
                    <div class="space-y-3">
                        <input type="text" id="newItemTitle" placeholder="Link Text" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                        <input type="text" id="newItemUrl" placeholder="URL (e.g., /shop or https://example.com)" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                        <select id="newItemTarget" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Window</option>
                        </select>
                        <button type="button" onclick="addCustomItem()" class="w-full px-3 py-2 bg-rose-500 text-white rounded hover:bg-rose-600 text-sm">
                            Add Custom Link
                        </button>
                    </div>
                </div>

                <!-- Categories -->
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pages</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto" id="pageLinks">
                        @foreach($pages ?? [] as $page)
                        <button type="button" onclick="addQuickLink('/page/{{ $page->slug }}')" class="w-full text-left px-3 py-2 bg-white border border-gray-200 rounded hover:border-rose-500 text-sm truncate">
                            {{ $page->title }}
                        </button>
                        @endforeach
                        <p class="text-xs text-gray-400 text-center">Pages will be loaded from database</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="mt-6 pt-6 border-t">
        <button type="button" onclick="saveMenu()" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700">
            <i class="fas fa-save mr-2"></i>Save Menu
        </button>
    </div>
</div>

<script>
let menus = {
    'footer1': {
        name: 'Footer Column 1 - Quick Links',
        slug: 'footer-1',
        location: 'footer1',
        items: []
    },
    'footer2': {
        name: 'Footer Column 2 - Customer Service',
        slug: 'footer-2',
        location: 'footer2',
        items: []
    },
    'header': {
        name: 'Main Navigation',
        slug: 'header',
        location: 'header',
        items: []
    },
    'mobile': {
        name: 'Mobile Menu',
        slug: 'mobile',
        location: 'mobile',
        items: []
    }
};

let currentTab = 'footer1';
let menuCounter = 0;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadMenusFromServer();
    setupTabs();
});

function setupTabs() {
    document.querySelectorAll('.menu-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.menu-tab').forEach(t => {
                t.classList.remove('border-rose-500', 'text-rose-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-rose-500', 'text-rose-600');
            currentTab = this.dataset.tab;
            renderMenuItems();
        });
    });
}

async function loadMenusFromServer() {
    try {
        const response = await fetch('/admin/menus/api');
        const data = await response.json();
        if (data.success && data.data) {
            data.data.forEach(menu => {
                if (menus[menu.location]) {
                    menus[menu.location].items = menu.items || [];
                }
            });
            renderMenuItems();
        }
    } catch (e) {
        console.log('Loading default menus');
    }
    renderMenuItems();
}

function renderMenuItems() {
    const container = document.getElementById('menuItems');
    const emptyMsg = document.getElementById('emptyMenu');
    const titleEl = document.getElementById('menuTitle');
    
    const menu = menus[currentTab];
    titleEl.textContent = menu.name;
    container.innerHTML = '';
    
    if (menu.items.length === 0) {
        emptyMsg.classList.remove('hidden');
        container.classList.add('hidden');
    } else {
        emptyMsg.classList.add('hidden');
        container.classList.remove('hidden');
        
        menu.items.forEach((item, index) => {
            container.appendChild(createMenuItemElement(item, index));
        });
    }
}

function createMenuItemElement(item, index) {
    const div = document.createElement('div');
    div.className = 'bg-white border border-gray-200 rounded p-3 flex items-center gap-2';
    div.dataset.index = index;
    
    div.innerHTML = `
        <i class="fas fa-bars text-gray-400 cursor-move"></i>
        <input type="text" value="${item.title}" class="flex-1 px-2 py-1 border border-gray-200 rounded text-sm" onchange="updateItem(${index}, 'title', this.value)">
        <input type="text" value="${item.url}" class="flex-1 px-2 py-1 border border-gray-200 rounded text-sm text-gray-600" onchange="updateItem(${index}, 'url', this.value)">
        <select class="px-2 py-1 border border-gray-200 rounded text-xs" onchange="updateItem(${index}, 'target', this.value)" value="${item.target || '_self'}">
            <option value="_self" ${item.target === '_self' ? 'selected' : ''}>Same</option>
            <option value="_blank" ${item.target === '_blank' ? 'selected' : ''}>New</option>
        </select>
        <button type="button" onclick="removeItem(${index})" class="text-red-500 hover:text-red-700 px-2">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    return div;
}

function addMenuItem() {
    menus[currentTab].items.push({
        title: 'New Link',
        url: '/',
        target: '_self',
        children: []
    });
    renderMenuItems();
}

function addQuickLink(url) {
        const titles = {
        '/': 'Home',
        '/shop': 'Shop',
        '/categories': 'Categories',
        '/about': 'About Us',
        '/contact': 'Contact',
        '/faq': 'FAQ',
        '/shipping': 'Shipping Info',
        '/returns': 'Returns'
    };
    
    menus[currentTab].items.push({
        title: titles[url] || url,
        url: url,
        target: '_self'
    });
    renderMenuItems();
}

function addCustomItem() {
    const title = document.getElementById('newItemTitle').value.trim();
    const url = document.getElementById('newItemUrl').value.trim();
    const target = document.getElementById('newItemTarget').value;
    
    if (!title || !url) {
        alert('Please enter both title and URL');
        return;
    }
    
    menus[currentTab].items.push({
        title: title,
        url: url,
        target: target
    });
    
    document.getElementById('newItemTitle').value = '';
    document.getElementById('newItemUrl').value = '';
    
    renderMenuItems();
}

function updateItem(index, field, value) {
    menus[currentTab].items[index][field] = value;
}

function removeItem(index) {
    if (confirm('Are you sure you want to remove this item?')) {
        menus[currentTab].items.splice(index, 1);
        renderMenuItems();
    }
}

async function saveMenu() {
    try {
        const response = await fetch('/admin/menus/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ menus: Object.values(menus) })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message || 'Menu saved successfully!');
        } else {
            alert('Error saving menu');
        }
    } catch (e) {
        alert('Error: ' + e.message);
    }
}
</script>

<style>
.menu-tab.active {
    border-color: #f43f5e;
    color: #f43f5e;
}
</style>
@endsection