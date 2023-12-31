import axios from 'axios';
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';

export default function useAuth() {
    const processing = ref(false);
    const validationErrors = ref({});
    const router = useRouter();
    const loginForm = reactive({
        'email': '',
        'password': '',
        'remember': false,
    });

    const submitLogin = async () => {
        if (processing.value) {return}

        processing.value = true;

        validationErrors.value = {};

        axios.post('/login', loginForm)
            .then(async response => {
                console.log('Success', response)
                loginUser(response);
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors;
                }
            })
            .finally(() => {
                processing.value = false;
            });
    }

    const loginUser = (response) => {
        console.log('Success2', response)
        localStorage.setItem('loggedIn', JSON.stringify(true))
        router.push({ name: 'posts.index'})
    }

    return { submitLogin, validationErrors, processing, loginForm }

}

