<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Find Friends') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container my-24 mx-auto md:px-6">
                        <section class="mb-32 text-center">
                            <div class="lg:gap-xl-12 grid gap-x-6 md:grid-cols-2 lg:grid-cols-4" id="search__results">

                            </div>

                        </section>
                        <section class="mb-32 text-center">
                            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" onclick="loadMore()">Load More</button>
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
                    content.querySelector('.add__friend').setAttribute('onclick', `addFriend('${details.id}', this)`);
                break;
                case 'Request Sent':
                    content.querySelector('.add__friend').setAttribute('onclick', '#');
                    content.querySelector('.add__friend').disabled = 'disabled';
                    content.querySelector('.add__friend').innerText = 'Request Sent';
                    content.querySelector('.cancel__friend').classList.remove('hidden');
                    content.querySelector('.cancel__friend').setAttribute('onclick', `cancelRequest('${details.request_id}', this)`);
                break;
                default:
                    console.log('hello');
                break;
            }

            content.querySelector('.add__friend').setAttribute('onclick', `addFriend('${details.id}', this)`);
            const searchResultsDiv = document.getElementById('search__results');
            searchResultsDiv.appendChild(content);
        }
    }

    let page = 1;
    let search_name = '';

    function loadMore(){
        page++;
        searchFriends();
    }

    function searchFriends(){
        fetch(`{{ route('friends.search') }}?search_name=${encodeURIComponent(search_name)}&page=${page}`,{
            headers : {
                'X-CSRF-TOKEN': '{{ csrf_token()}}'
            },
            method : 'GET'
        }).then(res=>{
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        }).then(data=>{
            data.data.forEach(profileDetails=>{
                if(profileDetails.status !== 'Accept'){
                    new User(profileDetails)
                }
            })
        }).catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
    }

    searchFriends();

    function addFriend(id, ele){
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
                ele.previousElementSibling.removeAttribute('disabled');
            } else{
                alert('something went wrong, please refresh');
            }

        }).catch(error=>{
            console.error('There was a problem with the fetch operation:', error);
        })
    }


</script>

