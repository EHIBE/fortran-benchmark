program benchmark
    implicit none
    integer, parameter :: n = 50000000
    integer :: count1, count2, count_rate
    real(8) :: res = 0.0
    integer :: i

    ! use system_clock for high resolution timing
    call system_clock(count1, count_rate)
    
    do i = 1, n
        res = res + sqrt(real(i))
    end do
    
    call system_clock(count2)

    ! write math result to stderr
    write(0, *) res

    ! calculate and print the exact seconds to stdout
    print *, real(count2 - count1) / real(count_rate)
end program benchmark