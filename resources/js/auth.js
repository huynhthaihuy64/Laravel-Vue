export const setUserInfo = (data) => {
    window.localStorage.setItem('userInfo', data)
}

export const setAccessToken = (token, expiresIn) => {
    if (token && expiresIn) {
        window.localStorage.setItem('access_token', token)
        window.localStorage.setItem('expires_in', expiresIn)
    } else {
        console.error('Invalid token or expiresIn: ', token, expiresIn);
    }
}

export const getUserInfo = () => {
    return window.localStorage.getItem('userInfo')
}

export const getAccessToken = () => {
    // const userInfo = getUserInfo()
    const token = window.localStorage.getItem('access_token');
    const expiresIn = window.localStorage.getItem('expires_in');
    if(!token || !expiresIn) {
        return null
    }
    console.log("true", Date.now() > Date.parse(expiresIn));
    console.log('expires', expiresIn)
    if (Date.now() > Date.parse(expiresIn))
    {
        clearAccessToken();
        return null;
    }
    console.log('otken', token)
    return token;
}

export const clearAccessToken = () => {
    window.localStorage.removeItem('access_token');
    window.localStorage.removeItem('expires_in');
    window.localStorage.removeItem('userInfo');
};

export const revokeUser = () => {
    window.localStorage.removeItem('userInfo')
    window.localStorage.removeItem('access_token');
    window.localStorage.removeItem('expires_in');
}

export const noAuth = (context) => {
    const { next, isLoggedIn } = context
    if (isLoggedIn) {
        return next({ name: 'Home' })
    }
    return next()
}

export const permissionHome = (context) => {
    const { next, isLoggedIn } = context
    console.log("isLoggedIn", isLoggedIn);
    if (isLoggedIn) {
        return next()
    }
    return next({ name: 'SignIn' })
}

export const isAuthenticated = () => {
    // const userInfo = getUserInfo()
    const token = getAccessToken()
    // return !!userInfo && !!token
    return !!token
}

export function middlewarePipeline(context, middleware, index) {
    const nextMiddleware = middleware[index];
    if (!nextMiddleware) {
        return context.next;
    }

    return () => {
        const nextPipeline = middlewarePipeline(context, middleware, index + 1);
        nextMiddleware({ ...context, next: nextPipeline });
    };
}