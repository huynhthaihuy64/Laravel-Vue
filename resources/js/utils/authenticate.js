export const setUserInfo = (data) => {
    window.localStorage.setItem("userInfo", data);
};

export const getUserInfo = () => {
    return window.localStorage.getItem("userInfo");
};

export const revokeUser = () => {
    window.localStorage.removeItem("userInfo");
};

export const isAuthenticated = () => {
    const userInfo = getUserInfo();
    return !!userInfo;
};

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
