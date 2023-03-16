export const setUserInfo = (data) => {
    window.localStorage.setItem('userInfo', data)
}

export const setAccessToken = (token) => {
    window.localStorage.setItem('token', token)
}

export const getUserInfo = () => {
    return window.localStorage.getItem('userInfo')
}

export const getAccessToken = () => {
    const userInfo = getUserInfo()
    return userInfo && window.localStorage.getItem('token')
}

export const revokeUser = () => {
    window.localStorage.removeItem('userInfo')
    window.localStorage.removeItem('token')
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
    if (isLoggedIn) {
        return next()
    }
    return next({ name: 'SignIn' })
}

export const isAuthenticated = () => {
    const userInfo = getUserInfo()
    const token = getAccessToken()
    return !!userInfo && !!token
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