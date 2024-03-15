<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container my-6 mx-auto md:px-6">
                        <div class="border-b mb-12 border-gray-200 dark:border-gray-700">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                                <li class="me-2 tab" onclick="activeTab(this, 'requests_received')">
                                    <a href="javascript:void(0);" class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500">
                                        Received Requests
                                    </a>
                                </li>
                                <li class="me-2 tab" onclick="activeTab(this, 'requests_sent')">
                                    <a href="javascript:void(0);" class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" aria-current="page">
                                        Sent Requests
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <section class="mb-32 text-center">
                            <div class="lg:gap-xl-12 grid gap-x-6 md:grid-cols-2 lg:grid-cols-4" id="search__results">

                            </div>
                        </section>
                        <section class="text-center">
                            <button type="button" id="load__more__btn" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" onclick="loadMore()">Load More</button>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <template>
        <div class="mb-12 lg:mb-0">
            <img class="mx-auto mb-6 rounded-lg shadow-lg dark:shadow-black/20 w-[150px] user__image"
                src=""
                alt="avatar"
                style="border-radius: 50%;object-fit: cover;height: 150px;width:150px;" />
            <h5 class="mb-4 text-lg font-bold user__name">Alan Turing</h5>
            <button type="button" class="add__friend text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" onclick="">Add Friend</button>
            <button type="button" class="cancel__friend hidden focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900" onclick="">Cancel Request</button>
        </div>
    </template>
</x-app-layout>


<script>
    class User
    {
        constructor(details){
            const template = document.querySelector('template');
            const content = document.importNode(template.content, true);
            content.querySelector('.user__name').innerText = details.name;
            content.querySelector('.user__image').src = `{{ asset('profile-pictures') }}/`+details.profile_picture;

            switch(details.status){
                case 'Add':
                    content.querySelector('.add__friend').setAttribute('onclick', `sendRequest('${details.id}', this)`);
                break;
                case 'Request Sent':
                    content.querySelector('.add__friend').setAttribute('onclick', '#');
                    content.querySelector('.add__friend').disabled = 'disabled';
                    content.querySelector('.add__friend').innerText = 'Request Sent';
                    content.querySelector('.cancel__friend').classList.remove('hidden');
                    content.querySelector('.cancel__friend').setAttribute('onclick', `cancelRequest('${details.request_id}', this)`);
                break;
                case 'Accept':
                    content.querySelector('.add__friend').setAttribute('onclick', `acceptRequest('${details.request_id}', this)`);
                    content.querySelector('.add__friend').innerText = 'Accept';
                    content.querySelector('.cancel__friend').classList.remove('hidden');
                    content.querySelector('.cancel__friend').setAttribute('onclick', `rejectRequest('${details.request_id}', this)`);
                default:
                    // alert('Something went wrong!');
                break;
            }

            content.querySelector('.add__friend').dataset.id = details.id;
            content.querySelector('.cancel__friend').dataset.id = details.id;
            // content.querySelector('.add__friend').setAttribute('onclick', `sendRequest('${details.id}', this)`);
            const searchResultsDiv = document.getElementById('search__results');
            searchResultsDiv.appendChild(content);
        }
    }

    let page = 1, request_type = 'requests_received';

    function activeTab(ele, type){
        document.querySelectorAll('.tab').forEach(tabEle=>{
            tabEle.querySelector('a').classList.remove('text-blue-600', 'border-blue-600', 'dark:text-blue-500', 'dark:border-blue-500');
            tabEle.querySelector('a').classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
            tabEle.querySelector('a').classList.remove('text-blue-600', 'dark:text-blue-500');
            tabEle.querySelector('a').classList.add('text-gray-400', 'group-hover:text-gray-500' ,'dark:text-gray-500', 'dark:group-hover:text-gray-300')
        })
        ele.querySelector('a').classList.add('text-blue-600', 'border-blue-600', 'dark:text-blue-500', 'dark:border-blue-500')
        ele.querySelector('a').classList.add('text-blue-600', 'dark:text-blue-500');
        request_type = type;
        page = 1;
        getUsers(request_type, page);

    }

    getUsers('requests_received', 1);


    function loadMore(){
        page++;
        getUsers(request_type, page);
    }

    function getUsers(type, page){
        if(page === 1){
            document.getElementById('search__results').innerHTML = '';
        }
        fetch(`{{ route('friends.requests') }}?type=${type}&page=${page}`, {
            headers : {
                'Accept': 'application/json',
                'X-CSRF-TOKEN' : '{{ csrf_token() }}'
            },
            method : 'POST'
        }).then(res=>{
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        }).then(data=>{
            if(data.data.length === 0 && page === 1){
                document.getElementById('search__results').innerHTML = '<div class="text-center">No More Requests</div>';
            }
            if(data.meta.last_page === page){
                document.getElementById('load__more__btn').classList.add('hidden');
            } else {
                document.getElementById('load__more__btn').classList.remove('hidden');
            }
            data.data.forEach(profileDetails=>{
                new User(profileDetails)
            })
        }).catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
    }

    function sendRequest(id, ele){
        fetch(`{{ route('friends.add') }}`,{
            headers : {
                'X-CSRF-TOKEN': '{{ csrf_token()}}',
                'Content-Type': 'application/json'
            },
            method : 'POST',
            body : JSON.stringify({
                id : id
            })
        }).then(res=>{
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        }).then(data=>{
            if(!data.is_exists){
                ele.innerText = 'Request Sent';
                ele.disabled = 'disabled';
                ele.nextElementSibling.classList.remove('hidden');
                ele.nextElementSibling.setAttribute('onclick', `cancelRequest('${data.request_id}', this)`);
            }
        }).catch(error=>{
            console.error('There was a problem with the fetch operation:', error);
        })
    }

    function cancelRequest(id, ele) {
        fetch(`{{ route('friends.cancel') }}`,{
            headers : {
                'X-CSRF-TOKEN': '{{ csrf_token()}}',
                'Content-Type': 'application/json'
            },
            method : 'POST',
            body : JSON.stringify({
                id : id
            })
        }).then(res=>{
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        }).then(data=>{
            if(data.success){
                ele.classList.add('hidden');
                ele.previousElementSibling.innerText = 'Add Friend';
                ele.previousElementSibling.setAttribute('onclick', `sendRequest('${ele.dataset.id}', this)`);
                ele.previousElementSibling.removeAttribute('disabled');
            } else{
                alert('something went wrong, please refresh');
            }

        }).catch(error=>{
            console.error('There was a problem with the fetch operation:', error);
        })
    }

    function acceptRequest(id, ele){
        fetch(`{{ route('friends.accept') }}`,{
            headers : {
                'X-CSRF-TOKEN': '{{ csrf_token()}}',
                'Content-Type': 'application/json'
            },
            method : 'POST',
            body : JSON.stringify({
                id : id
            })
        }).then(res=>{
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        }).then(data=>{
            if(data.success){
                ele.innerText = 'Friends';
                ele.disabled = 'disabled';
                ele.nextElementSibling.classList.add('hidden');
            } else{
                alert('something went wrong, please refresh');
            }

        }).catch(error=>{
            console.error('There was a problem with the fetch operation:', error);
        })
    }

    function rejectRequest(id, ele) {
        fetch(`{{ route('friends.reject') }}`,{
            headers : {
                'X-CSRF-TOKEN': '{{ csrf_token()}}',
                'Content-Type': 'application/json'
            },
            method : 'POST',
            body : JSON.stringify({
                id : id
            })
        }).then(res=>{
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        }).then(data=>{
            if(data.success){
                ele.innerText = 'Request rejected';
                ele.disabled = 'disabled';
                ele.previousElementSibling.classList.add('hidden');
            } else{
                alert('something went wrong, please refresh');
            }

        }).catch(error=>{
            console.error('There was a problem with the fetch operation:', error);
        })
    }
</script>
