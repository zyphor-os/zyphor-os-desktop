#include "os_info_fetcher.hpp"

std::pair<std::string, std::string> 
OS_INFO_FETCHER::get_os_info(std::string key)  {


    // finds the specific keyword in /etc/os-release for example "PRETTY_NAME" inside the string dump
    size_t start_buffer = buffer.find(key);
    
    // checks if key dont exists
    if (start_buffer == std::string::npos) {
        return {key, "unknown"};
    }

    // Linear Reading on each character at the start of the keyword to find the value within
    // and stops when hitting a newspace, end-of-file etc
    std::stringstream output_stream;
    for (char c: buffer.substr(start_buffer + key.size()) ) {
        if (c == '\n' || c == '\r' || c == '\0') {
            output_stream << c;
            break;
        } 
        output_stream << c;
    }

    // returns the keyword and it's value as a pair key for std::map
    auto result = std::pair<std::string, std::string>( {key, remove_quotes(output_stream.str())});
    return result;
};