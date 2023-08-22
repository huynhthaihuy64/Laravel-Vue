import axios from "axios";
import { get } from "lodash";
import { getAccessToken, revokeUser } from "./auth";
const headers = {
  Accept: "application/json",
  "Content-Type": "application/json; charset=utf-8",
};

const axiosInstance = axios.create({
  headers,
});

axiosInstance.interceptors.request.use(
  (config) => {
    let params = config.params || {};
    const token = getAccessToken();
    if (token) {
      config.headers["Authorization"] = "Bearer " + token;
    }
    return {
      ...config,
      params: params,
    };
  },
  (error) => {
    return Promise.reject(error);
  }
);

axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => {
    const status = get(error, "response.status");
    const errorData = get(error, "response.data");
    switch (status) {
      case 401: {
        revokeUser();
        window.location.href = '/SignIn';
        break;
      }
      case errorContants.StatusCode.ValidationFailed:
      case errorContants.StatusCode.BadRequest: {
        return Promise.reject({ ...errorData, status });
      }
      case errorContants.StatusCode.Forbidden: {
        break;
      }
      case errorContants.StatusCode.InternalServerError: {
        break;
      }
      case errorContants.StatusCode.TooManyRequests: {
        break;
      }
    }
    return Promise.reject({ ...errorData, status });
  }
);

function getApi(url, config = {}) {
  return axiosInstance.get(url, config);
}

function postApi(url, data, config = {}) {
  return axiosInstance.post(url, data, config);
}

function putApi(url, data, config = {}) {
  return axiosInstance.put(url, data, config);
}

function deleteApi(url, data = {}, config = {}) {
  return axiosInstance.delete(url, data, config);
}

const httpRequest = {
  get: getApi,
  post: postApi,
  put: putApi,
  delete: deleteApi,
};

export default httpRequest;
