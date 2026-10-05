<table id="result-container" class="table table-bordered table-hover ">
    <thead>
        <tr>
            @if(!empty($dataset) && isset($dataset[0]))
            <th style="width: 3px;">SL</th>
            @endif
            @if(!empty($dataset) && isset($dataset[0]))
                <?php
                    $count = [
                        '1' => 0,
                        '2' => 0,
                    ];

                    foreach ($dataset[0] as $key=>$val) {
                        $parts = explode('|', $key);
                            if (count($parts) > 1) {
                                $secondPart = trim($parts[1]);
                                if (array_key_exists($secondPart, $count)) {
                                    $count[$secondPart]++;
                                }
                            }
                    }

                ?>
                @foreach($dataset[0] as $key => $value)
                    <?php
                        $parts = explode('|', $key);
                        $status = count($parts) > 1 ? true :false;
                    ?>
                    {{-- <th style="width: 100px;" class="{{ $status ? 'text-right' : '' }}">{{ ucfirst(str_replace('|', ' ', $key)) }} </th> --}}
                    <th style="width: 100px;" class="{{ $status ? 'text-right' : '' }}">{{ ucfirst(trim(explode('|', $key)[0])) }}</th>
                    <?php

                    if (count($parts) > 1 && trim($parts[1]) == '1' && --$count['1'] == 0) {
                        $class = 'text-right'; // Define the class variable
                        echo '<th style="width: 120px;" class="' . $class . '">Total Addition</th>';
                    }

                    if (count($parts) > 1 && trim($parts[1]) == '2' && --$count['2'] == 0) {
                        $class = 'text-right';
                        echo '<th style="width: 120px;" class="' . $class . '">Total Deduction</th>';
                    }
                    ?>
                @endforeach
                <th style="width: 100px;" class="text-right">Net Amount</th>
            @endif
        </tr>
    </thead>
    <tbody>

        <?php
            $grandAdditionTotal = [];
            $grandDeductionTotal = [];
            $grandKeyWiseTotal = [];
        ?>

        @foreach ($dataset as $data)
            <?php

                $count = [
                    '1' => 0,
                    '2' => 0,
                ];

                foreach ($dataset[0] as $key=>$val) {
                    $parts = explode('|', $key);
                        if (count($parts) > 1) {
                            $secondPart = trim($parts[1]);
                            if (array_key_exists($secondPart, $count)) {
                                $count[$secondPart]++;
                            }
                        }
                }

                // dd($count);
                $totalAddition = 0;
                $totalDeduction = 0;

                // Track the last column indexes for |1 and |2
                $lastAdditionIndex = -1;
                $lastDeductionIndex = -1;

                // Identify the last index positions for |1 and |2
                $index = 0;
                foreach ($data as $key=>$val) {
                    $parts = explode('|', $key);
                    if (count($parts) > 1) {
                        if (trim($parts[1]) == '1') {
                            $lastAdditionIndex = $index;
                        } elseif (trim($parts[1]) == '2') {
                            $lastDeductionIndex = $index;
                        }
                    }
                    $index++;
                }
            ?>

            <tr>
                <?php
                $index = 0;
                $totalAddition = 0;
                $totalDeduction = 0;
                ?>
                <td>{{ $loop->iteration  }}</td>
                @foreach ($data as $key =>$value)
                    <?php
                        $parts = explode('|', $key);
                        $status = count($parts) > 1 ? true :false;
                    ?>

                    <td class="{{ $status ? 'text-right' : '' }}">{{ $value }}</td>

                    {{-- Calculate totals for additions and deductions --}}
                    <?php
                    $parts = explode('|', $key);


                    if (count($parts) > 1 && trim($parts[1]) == '1') {
                        $totalAddition += (float) $value;
                        $grandKeyWiseTotal[1][$parts[0]][] = (float) $value;
                    }

                    if (count($parts) > 1 && trim($parts[1]) == '2') {
                        $totalDeduction += (float) $value;
                        $grandKeyWiseTotal[2][$parts[0]][] =  (float) $value;
                    }
                    ?>

                    {{-- Insert Total Addition after the last |1 column --}}
                    @if ($index === $lastAdditionIndex)
                        <td class="text-right"><strong>{{  $grandAdditionTotal[] = $totalAddition }}</strong></td>
                    @endif

                    {{-- Insert Total Deduction after the last |2 column --}}
                    @if ($index === $lastDeductionIndex)
                        <td class="text-right"><strong>{{ $grandDeductionTotal[] = $totalDeduction }}</strong></td>
                    @endif

                    <?php $index++; ?>
                @endforeach

                <td class="text-right"><strong>{{ $totalAddition - $totalDeduction }}</strong></td>
            </tr>
        @endforeach
    </tbody>
    <footer>
        @if(!empty($dataset) && isset($dataset[0]))
                <?php
                    $_grandAdditionTotal = array_sum($grandAdditionTotal);
                    $_grandDeductionTotal = array_sum($grandDeductionTotal);
                    $count = [
                        '1' => 0,
                        '2' => 0,
                    ];
                    foreach ($dataset[0] as $key => $val){
                        $parts = explode('|', $key);
                            if (count($parts) > 1) {
                                $secondPart = trim($parts[1]);
                                if (array_key_exists($secondPart, $count)) {
                                    $count[$secondPart]++;
                                }
                            }
                    }

                ?>
                  <th class="text-center">Total</th>
                @foreach($dataset[0] as $key => $value)
                    {{-- <th style="width: 100px;"></th> --}}
                    <?php


                    $parts = explode('|', $key);
                    $status1 =(count($parts) > 1 && trim($parts[1]) == '1') ? true : false;
                    $status2 =(count($parts) > 1 && trim($parts[1]) == '2') ? true : false;

                    if (!$status1 && !$status2) {
                        echo "<th style='width: 120px;'></th>";
                    }
                    if (count($parts) > 1) {
                        $value = 0;

                        $value = array_sum($grandKeyWiseTotal[$parts[1]][$parts[0]]);
                        $class = 'text-right'; // Define the class variable
                        echo "<th style='width: 120px;' class='$class'>$value</th>";
                    }

                    if (count($parts) > 1 && trim($parts[1]) == '1' && --$count['1'] == 0) {
                        $class = 'text-right'; // Define the class variable
                        echo "<th style='width: 120px;' class='$class'>$_grandAdditionTotal</th>";
                    }

                    if (count($parts) > 1 && trim($parts[1]) == '2' && --$count['2'] == 0) {
                        $class = 'text-right';
                        echo "<th style='width: 120px;'  class='$class'> $_grandDeductionTotal</th>";
                    }
                    ?>
                @endforeach
                <th style="width: 100px;" class="text-right">{{ $_grandAdditionTotal -  $_grandDeductionTotal}}</th>
            @endif
    </footer>

</table>
